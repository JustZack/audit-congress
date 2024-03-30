import os, time, shutil, io, json, math
from datetime import datetime

from pprint import pprint

import sys
sys.path.append(os.path.abspath("../"))

from shared import logger, db, zjthreads, util, cache
from members import memberparse as mparse

SCRIPT_NAME = "bulk-member"

BASE_URL = "https://theunitedstates.io/congress-legislators/"

PRESIDENTS_URL = "{}executive.json".format(BASE_URL)
CURRENT_LEGISLATORS_URL = "{}legislators-current.json".format(BASE_URL)
HISTORICAL_LEGISLATORS_URL = "{}legislators-historical.json".format(BASE_URL)
LEGISLATORS_SOCIALS_URL = "{}legislators-social-media.json".format(BASE_URL)
LEGISLATORS_OFFICES_URL = "{}legislators-district-offices.json".format(BASE_URL)

CURRENT_COMMITTEES_URL = "{}committees-current.json".format(BASE_URL)
HISTORICAL_COMMITTEES_URL = "{}committees-historical.json".format(BASE_URL)
CURRENT_COMMITTEE_LEGISLATORS_URL = "{}committee-membership-current.json".format(BASE_URL)

MEMBER_COLUMNS = ["bioguideId", "thomasId", "lisId", "govTrackId", "openSecretsId", "voteSmartId",
                  "cspanId", "mapLightId", "icpsrId", "wikidataId", "googleEntityId",
                  "official_full", "first", "last", "gender", "birthday", "isCurrent",
                  "lastUpdate", "nextUpdate"]
TERM_COLUMNS = ["bioguideId", "type", "start", "end", "state", "district", "party", "class", "how",
                "state_rank", "url", "rss_url", "contact_form", "address", "office", "phone",
                "lastUpdate", "nextUpdate"]
ELECTION_COLUMNS = ["fecId", "bioguideId", "lastUpdate", "nextUpdate"]
SOCIAL_COLUMNS = ["bioguideId", "twitter", "twitterId", "facebook", "facebookId", "youtube", "youtubeId", 
                  "instagram", "instagramId", "lastUpdate", "nextUpdate"]
OFFICES_COLUMNS = ["officeId", "bioguideId", "address", "suite", "building", "city", "state", "zip", 
                   "latitude", "longitude", "phone", "fax", "lastUpdate", "nextUpdate"]

COMMITTEE_COLUMNS = ["thomasId", "parentId", "type", "name", "wikipedia", "jurisdiction", "jurisdiction_source", 
                     "url", "rss_url", "minority_url", "minority_rss_url", "youtubeId", 
                     "address", "phone", "isCurrent"]
COMMITTEE_MEMBERSHIP_COLUMNS = ["thomasId", "bioguideId", "party", "title", "memberRank"]


#Works best with chunk size > len(currentMembers) and size <= 1000
MEMBER_CHUNK_SIZE = 1000

def doSingleThreadedMemberInsert(data, threadFunction, insertType):
    startInsert, threads, count = datetime.now(), [], len(data)

    logger.logInfo("Starting insert of", count, "member's {}.".format(insertType))
    zjthreads.startThenJoinThreads([threadFunction(data)])
    logger.logInfo("Took",util.seconds_since(startInsert),"seconds to insert", count, "member's {}.".format(insertType))

def doSimpleInsert(tableName, dataSourceUrl, threadInsertFunction, insertType):
    db.deleteRows(tableName)
    data = util.getParsedJson(dataSourceUrl)
    doSingleThreadedMemberInsert(data, threadInsertFunction, insertType)



def getMemberInsertThreads(members, isCurrent):
    threads = []
    memberData, termData, electData = [], [], []
    for member in members:
        bioguideId = member["id"]["bioguide"]
        memberData.append(mparse.getMemberRow(member, isCurrent))
        termData.extend(mparse.getTermRows(member["terms"], bioguideId))
        if "fec" in member["id"]:
            electData.extend(mparse.getElectionRows(member["id"]["fec"], bioguideId))

    threads.append(zjthreads.buildThread(db.insertRows, "Members", MEMBER_COLUMNS, memberData))
    threads.append(zjthreads.buildThread(db.insertRows, "MemberTerms", TERM_COLUMNS, termData))
    threads.append(zjthreads.buildThread(db.insertRows, "MemberElections", ELECTION_COLUMNS, electData))
    
    return threads

def getChunkedMemberInsertThreads(chunkedMembers, isCurrent):
    threads = []
    for chunk in range(len(chunkedMembers)): 
        threads.extend(getMemberInsertThreads(chunkedMembers[chunk], isCurrent))
    return threads

def parseAndInsertMembers(members, isCurrent):
    startInsert, threads, memCount = datetime.now(), [], len(members)

    chunkedMembers = util.chunkList(members, MEMBER_CHUNK_SIZE)
    threads.extend(getChunkedMemberInsertThreads(chunkedMembers, isCurrent))

    logger.logInfo("Starting insert of", memCount, "members and their sub data with", len(threads), "threads.")
    zjthreads.startThenJoinThreads(threads)
    logger.logInfo("Took",util.seconds_since(startInsert),"seconds to insert", memCount, "members.")

def doMemberInsertGroup(isCurrent):
    url = CURRENT_LEGISLATORS_URL if isCurrent else HISTORICAL_LEGISLATORS_URL
    members = util.getParsedJson(url)
    parseAndInsertMembers(members, isCurrent)

def doMemberInsert():
    db.deleteRowsFromTables(["Members", "MemberTerms", "MemberElections"])
    zjthreads.runThreads(doMemberInsertGroup, [True, False])



def getSocialInsertThread(socials):
    socData = []
    for social in socials: socData.append(mparse.getSocialRow(social))
    
    return zjthreads.buildThread(db.insertRows, "MemberSocials", SOCIAL_COLUMNS, socData)

def doSocialsInsert(): 
    doSimpleInsert("MemberSocials", LEGISLATORS_SOCIALS_URL, getSocialInsertThread, "socials")



def getOfficeInsertThread(offices):
    offData = []
    for memberOffices in offices: 
        bioguideId = memberOffices["id"]["bioguide"]
        mOffices = memberOffices["offices"]
        offData.extend(mparse.getOfficeRows(bioguideId, mOffices))

    return zjthreads.buildThread(db.insertRows, "MemberOffices", OFFICES_COLUMNS, offData)

def doOfficesInsert():
    doSimpleInsert("MemberOffices", LEGISLATORS_OFFICES_URL, getOfficeInsertThread, "offices")



def getCommitteeInsertThreads(committees):
    threads, commData, ComHistData = [], [], []
    for code in committees:
        com = committees[code]
        commData.append(mparse.getCommitteeRow(com))
        if "subcommittees" in com: 
            commData.extend(mparse.getSubCommitteeRows(com["subcommittees"], com["thomas_id"], com["type"]))

    logger.logInfo("Found",len(commData),"committees & subcommittees.")
    threads.append(zjthreads.buildThread(db.insertRows, "Committees", COMMITTEE_COLUMNS, commData))
    
    return threads

def insertCommittees(committees):
    startInsert, threads, comCount = datetime.now(), [], len(committees)

    threads.extend(getCommitteeInsertThreads(committees))

    logger.logInfo("Starting insert of", comCount, "committees and their sub data with", len(threads), "threads.")
    zjthreads.startThenJoinThreads(threads)
    logger.logInfo("Took",util.seconds_since(startInsert),"seconds to insert", comCount, "committees.")

def getCommitteesAsDict(url):
    committees = util.getParsedJson(url)
    return util.dictArrayToDict(committees, "thomas_id")

def getAggregatedCommittees(current, historic):
    aggregated = dict()
    cSet = set(current.keys())
    cSet.update(historic.keys())

    for code in cSet:
        currentSub, historicSub = [], []
        isCurrent = False
        data = None
        if code in current:
            if "subcommittees" in current[code]: currentSub = current[code]["subcommittees"]
            isCurrent = True
            data = current[code]
            
        if code in historic:
            if isCurrent:
                data["names"] = historic[code]["names"]
                data["congresses"] = historic[code]["congresses"]
            else: 
                data = historic[code]

            if "subcommittees" in historic[code]: historicSub = historic[code]["subcommittees"]
        
        if len(currentSub) > 0 or len(historicSub) > 0:
            subCurrent = util.dictArrayToDict(currentSub, "thomas_id")
            subHistoric = util.dictArrayToDict(historicSub, "thomas_id")
            data["subcommittees"] = getAggregatedCommittees(subCurrent, subHistoric)

        data["isCurrent"] = isCurrent
        aggregated[code] = data
    return aggregated

def parseCommittees():
    current = getCommitteesAsDict(CURRENT_COMMITTEES_URL)
    historic = getCommitteesAsDict(HISTORICAL_COMMITTEES_URL)
    return getAggregatedCommittees(current, historic)

def doCommitteeInsert():
    db.deleteRowsFromTables(["Committees"])
    committees = parseCommittees()
    insertCommittees(committees)



def getCommitteeMembershipInsertThread(membership):
    threads, memData = [], []
    for code in membership:
        members = membership[code]
        memData.extend(mparse.getMembersipRows(members, code))

    logger.logInfo("Found",len(memData),"membership entries.")
    return zjthreads.buildThread(db.insertRows, "CommitteeMembership", COMMITTEE_MEMBERSHIP_COLUMNS, memData)

def doCommitteeMembershipInsert():
    doSimpleInsert("CommitteeMembership", CURRENT_COMMITTEE_LEGISLATORS_URL, getCommitteeMembershipInsertThread, "committee membership")



def doSetup(): util.genericBulkScriptSetup(SCRIPT_NAME)

def doBulkMemberPull():
    startPull = datetime.now()
    threads = []

    threads.append(zjthreads.buildThread(doMemberInsert))
    threads.append(zjthreads.buildThread(doSocialsInsert))
    threads.append(zjthreads.buildThread(doOfficesInsert))
    threads.append(zjthreads.buildThread(doCommitteeInsert))
    threads.append(zjthreads.buildThread(doCommitteeMembershipInsert))

    zjthreads.startThenJoinThreads(threads)

    logger.logInfo("Took", util.seconds_since(startPull), "seconds to insert member based data.")

def main(): util.genericBulkScriptMain(doSetup, doBulkMemberPull, SCRIPT_NAME)

if __name__ == "__main__": util.runAndCatchMain(main, cache.setScriptRunning, SCRIPT_NAME, False)
