import os, time, shutil, io, json, math
from datetime import datetime

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
                  "official_full", "first", "last", "gender", "birthday", "isCurrent"]
TERM_COLUMNS = ["bioguideId", "type", "start", "end", "state", "district", "party", "class", "how",
                "state_rank", "url", "rss_url", "contact_form", "address", "office", "phone"]
ELECTION_COLUMNS = ["fecId", "bioguideId"]
SOCIAL_COLUMNS = ["bioguideId", "twitter", "twitterId", "facebook", "facebookId", "youtube", "youtubeId", 
                  "instagram", "instagramId"]
OFFICES_COLUMNS = ["officeId", "bioguideId", "address", "suite", "building", "city", "state", "zip", 
                   "latitude", "longitude", "phone", "fax"]

COMMITTEE_COLUMNS = ["thomasId", "parentId", "type", "name", "wikipedia", "jurisdiction", "jurisdiction_source", 
                     "url", "rss_url", "minority_url", "minority_rss_url", "youtubeId", 
                     "address", "phone", "isCurrent"]
COMMITTEE_HISTORY_COLUMNS = ["thomasId", "parentId", "type", "congress", "name"]
COMMITTEE_MEMBERSHIP_COLUMNS = ["thomasId", "bioguideId", "party", "title", "memberRank"]


#Works best with chunk size > len(currentMembers) and size <= 1000
MEMBER_CHUNK_SIZE = 1000

def doThreadedInsert(data, threadFunction, insertType):
    startInsert, threads, count = datetime.now(), [], len(data)

    logger.logInfo("Starting insert of", count, "member's {}.".format(insertType))
    zjthreads.startThenJoinThreads(threadFunction(data))
    logger.logInfo("Took",util.seconds_since(startInsert),"seconds to insert", count, "member's {}.".format(insertType))

def doSimpleInsert(tableName, dataSourceUrl, threadInsertFunction, insertType):
    db.deleteRows(tableName)
    data = util.getParsedJson(dataSourceUrl)
    doThreadedInsert(data, threadInsertFunction, insertType)



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
    
    return [zjthreads.buildThread(db.insertRows, "MemberSocials", SOCIAL_COLUMNS, socData)]

def doSocialsInsert(): 
    doSimpleInsert("MemberSocials", LEGISLATORS_SOCIALS_URL, getSocialInsertThread, "socials")



def getOfficeInsertThread(offices):
    offData = []
    for memberOffices in offices: 
        bioguideId = memberOffices["id"]["bioguide"]
        mOffices = memberOffices["offices"]
        offData.extend(mparse.getOfficeRows(bioguideId, mOffices))

    return [zjthreads.buildThread(db.insertRows, "MemberOffices", OFFICES_COLUMNS, offData)]

def doOfficesInsert():
    doSimpleInsert("MemberOffices", LEGISLATORS_OFFICES_URL, getOfficeInsertThread, "offices")



def getCommitteeInsertThreads(committees):
    threads, commData, commHistData = [], [], []
    for code in committees:
        com = committees[code]
        tId, typ = com["thomas_id"], com["type"]

        commData.append(mparse.getCommitteeRow(com))
        commHistData.extend(mparse.getCommitteeHistoryRows(com))
        if "subcommittees" in com: 
            subcomm = com["subcommittees"]
            commData.extend(mparse.getSubCommitteeRows(subcomm, tId, typ))
            commHistData.extend(mparse.getSubCommitteeHistoryRows(subcomm))

    threads.append(zjthreads.buildThread(db.insertRows, "Committees", COMMITTEE_COLUMNS, commData))
    threads.append(zjthreads.buildThread(db.insertRows, "CommitteeHistory", COMMITTEE_HISTORY_COLUMNS, commHistData))
    
    return threads

def getCommitteesAsDict(url):
    committees = util.getParsedJson(url)
    return util.dictArrayToDict(committees, "thomas_id")

def parseCommittees():
    current = getCommitteesAsDict(CURRENT_COMMITTEES_URL)
    historic = getCommitteesAsDict(HISTORICAL_COMMITTEES_URL)
    return mparse.getAggregatedCommittees(current, historic)

def doCommitteeInsert():
    db.deleteRowsFromTables(["Committees", "CommitteeHistory"])
    committees = parseCommittees()
    doThreadedInsert(committees, getCommitteeInsertThreads, "committees")



def getCommitteeMembershipInsertThread(membership):
    threads, memData = [], []
    for code in membership:
        members = membership[code]
        memData.extend(mparse.getMembersipRows(members, code))

    return [zjthreads.buildThread(db.insertRows, "CommitteeMembership", COMMITTEE_MEMBERSHIP_COLUMNS, memData)]

def doCommitteeMembershipInsert():
    doSimpleInsert("CommitteeMembership", CURRENT_COMMITTEE_LEGISLATORS_URL, getCommitteeMembershipInsertThread, "committee membership")



def doSetup(): 
    util.genericBulkScriptSetup(SCRIPT_NAME)

    if mparse.fetchCurrentCongress(): 
        logger.logInfo("Found the current congress to be {} via the API".format(mparse.CURRENT_CONGRESS))
    else: raise Exception("Could not fetch current congress from API")

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
