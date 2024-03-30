import os, time, shutil, io, json, math
from datetime import datetime

from pprint import pprint

import sys
sys.path.append(os.path.abspath("../"))

from shared import logger, db, zjthreads, util, cache

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

def appendMemberUpdateTimes(row):
    now = time.time()
    row.append(now)
    row.append(now + (60*60*24*7))
    return row

def doSingleThreadedMemberInsert(data, threadFunction, insertType):
    startInsert, threads, count = datetime.now(), [], len(data)

    logger.logInfo("Starting insert of", count, "member's {}.".format(insertType))
    zjthreads.startThenJoinThreads([threadFunction(data)])
    logger.logInfo("Took",util.seconds_since(startInsert),"seconds to insert", count, "member's {}.".format(insertType))

def doSimpleInsert(tableName, dataSourceUrl, threadInsertFunction, insertType):
    db.deleteRows(tableName)
    data = util.getParsedJson(dataSourceUrl)
    doSingleThreadedMemberInsert(data, threadInsertFunction, insertType)




def getMemberRow(member, isCurrent):
    mRow = []
    mId, mName, mBio = member["id"], member["name"], member["bio"]
    mRow.append(mId["bioguide"])
    mRow.append(util.getFieldIfExists(mId, "thomas"))
    mRow.append(util.getFieldIfExists(mId, "lis"))
    mRow.append(util.getFieldIfExists(mId, "govtrack"))
    mRow.append(util.getFieldIfExists(mId, "opensecrets"))
    mRow.append(util.getFieldIfExists(mId, "votesmart"))
    mRow.append(util.getFieldIfExists(mId, "cspan"))
    mRow.append(util.getFieldIfExists(mId, "maplight"))
    mRow.append(util.getFieldIfExists(mId, "icpsr"))
    mRow.append(util.getFieldIfExists(mId, "wikidata"))
    mRow.append(util.getFieldIfExists(mId, "google_entity_id"))

    mRow.append(util.getFieldIfExists(mName, "official_full"))
    mRow.append(util.getFieldIfExists(mName, "first"))
    mRow.append(util.getFieldIfExists(mName, "last"))

    mRow.append(util.getFieldIfExists(mBio, "gender"))
    mRow.append(util.getFieldIfExists(mBio, "birthday"))

    mRow.append(isCurrent)
    
    return appendMemberUpdateTimes(mRow)

def getTermRows(terms, bioguideId):
    mTerms = []
    for term in terms:
        mTerm = []
        mTerm.append(bioguideId)
        mTerm.append(util.getFieldIfExists(term, "type"))
        mTerm.append(util.getFieldIfExists(term, "start"))
        mTerm.append(util.getFieldIfExists(term, "end"))
        mTerm.append(util.getFieldIfExists(term, "state"))
        mTerm.append(util.getFieldIfExists(term, "district"))
        mTerm.append(util.getFieldIfExists(term, "party"))
        mTerm.append(util.getFieldIfExists(term, "class"))
        mTerm.append(util.getFieldIfExists(term, "how"))
        mTerm.append(util.getFieldIfExists(term, "state_rank"))
        mTerm.append(util.getFieldIfExists(term, "url"))
        mTerm.append(util.getFieldIfExists(term, "rss_url"))
        mTerm.append(util.getFieldIfExists(term, "contact_form"))
        mTerm.append(util.getFieldIfExists(term, "address"))
        mTerm.append(util.getFieldIfExists(term, "office"))
        mTerm.append(util.getFieldIfExists(term, "phone"))

        mTerms.append(appendMemberUpdateTimes(mTerm))
    return mTerms

def getElectionRows(elections, bioguideId):
    mElections = []
    for election in elections:
        mElection = []
        mElection.append(election)
        mElection.append(bioguideId)

        mElections.append(appendMemberUpdateTimes(mElection))
    return mElections



def getMemberInsertThreads(members, isCurrent):
    threads = []
    memberData, termData, electData = [], [], []
    for member in members:
        bioguideId = member["id"]["bioguide"]
        memberData.append(getMemberRow(member, isCurrent))
        termData.extend(getTermRows(member["terms"], bioguideId))
        if "fec" in member["id"]:
            electData.extend(getElectionRows(member["id"]["fec"], bioguideId))

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



def getSocialRow(social):
    sRow = []
    sId, sSocial = social["id"], social["social"]

    sRow.append(sId["bioguide"])
    sRow.append(util.getFieldIfExists(sSocial, "twitter"))
    sRow.append(util.getFieldIfExists(sSocial, "twitter_id"))
    sRow.append(util.getFieldIfExists(sSocial, "facebook"))
    sRow.append(util.getFieldIfExists(sSocial, "facebook_id"))
    sRow.append(util.getFieldIfExists(sSocial, "youtube"))
    sRow.append(util.getFieldIfExists(sSocial, "youtube_id"))
    sRow.append(util.getFieldIfExists(sSocial, "instagram"))
    sRow.append(util.getFieldIfExists(sSocial, "instagram_id"))

    return appendMemberUpdateTimes(sRow)

def getSocialInsertThread(socials):
    socData = []
    for social in socials: socData.append(getSocialRow(social))
    
    return zjthreads.buildThread(db.insertRows, "MemberSocials", SOCIAL_COLUMNS, socData)

def doSocialsInsert(): 
    doSimpleInsert("MemberSocials", LEGISLATORS_SOCIALS_URL, getSocialInsertThread, "socials")



def getOfficeRows(bioguideId, offices):
    mOffices = []
    for office in offices:
        mOffice = []

        mOffice.append(util.getFieldIfExists(office, "id"))
        mOffice.append(bioguideId)
        mOffice.append(util.getFieldIfExists(office, "address"))
        mOffice.append(util.getFieldIfExists(office, "suite"))
        mOffice.append(util.getFieldIfExists(office, "building"))
        mOffice.append(util.getFieldIfExists(office, "city"))
        mOffice.append(util.getFieldIfExists(office, "state"))
        mOffice.append(util.getFieldIfExists(office, "zip"))
        mOffice.append(util.getFieldIfExists(office, "latitude"))
        mOffice.append(util.getFieldIfExists(office, "longitude"))
        mOffice.append(util.getFieldIfExists(office, "phone"))
        mOffice.append(util.getFieldIfExists(office, "fax"))

        mOffices.append(appendMemberUpdateTimes(mOffice))
    return mOffices

def getOfficeInsertThread(offices):
    offData = []
    for memberOffices in offices: 
        bioguideId = memberOffices["id"]["bioguide"]
        mOffices = memberOffices["offices"]
        offData.extend(getOfficeRows(bioguideId, mOffices))

    return zjthreads.buildThread(db.insertRows, "MemberOffices", OFFICES_COLUMNS, offData)

def doOfficesInsert():
    doSimpleInsert("MemberOffices", LEGISLATORS_OFFICES_URL, getOfficeInsertThread, "offices")




def getCommitteeRow(committee):
    cRow = []

    cRow.append(committee["thomas_id"])
    cRow.append(util.getFieldIfExists(committee, "parent_id"))
    cRow.append(util.getFieldIfExists(committee, "type"))
    cRow.append(util.getFieldIfExists(committee, "name"))
    cRow.append(util.getFieldIfExists(committee, "wikipedia"))
    cRow.append(util.getFieldIfExists(committee, "jurisdiction"))
    cRow.append(util.getFieldIfExists(committee, "jurisdiction_source"))
    cRow.append(util.getFieldIfExists(committee, "url"))
    cRow.append(util.getFieldIfExists(committee, "rss_url"))
    cRow.append(util.getFieldIfExists(committee, "minority_url"))
    cRow.append(util.getFieldIfExists(committee, "minority_rss_url"))
    cRow.append(util.getFieldIfExists(committee, "youtube_id"))
    cRow.append(util.getFieldIfExists(committee, "address"))
    cRow.append(util.getFieldIfExists(committee, "phone"))
    cRow.append(committee["isCurrent"])

    return cRow

def getSubCommitteeRows(subcommitees, parentId, parentType):
    subComData = []
    for sub in subcommitees: 
        subCom = subcommitees[sub]
        subCom["type"] = parentType
        subCom["parent_id"] = parentId
        subCom["thomas_id"] = parentId+subCom["thomas_id"]
        subComData.append(getCommitteeRow(subCom))
    return subComData

def getCommitteeInsertThreads(committees):
    threads, commData, ComHistData = [], [], []
    for code in committees:
        com = committees[code]
        commData.append(getCommitteeRow(com))
        if "subcommittees" in com: 
            commData.extend(getSubCommitteeRows(com["subcommittees"], com["thomas_id"], com["type"]))

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



def getMembersipRows(members, committee):
    membershipData = []
    for mem in members:
        cMember = []
        cMember.append(committee)
        cMember.append(util.getFieldIfExists(mem, "bioguide"))
        cMember.append(util.getFieldIfExists(mem, "party"))
        cMember.append(util.getFieldIfExists(mem, "title"))
        cMember.append(util.getFieldIfExists(mem, "rank"))
        membershipData.append(cMember)
    return membershipData

def getCommitteeMembershipInsertThreads(membership):
    threads, memData = [], []
    for code in membership:
        members = membership[code]
        memData.extend(getMembersipRows(members, code))

    logger.logInfo("Found",len(memData),"membership entries.")
    threads.append(zjthreads.buildThread(db.insertRows, "CommitteeMembership", COMMITTEE_MEMBERSHIP_COLUMNS, memData))
    
    return threads

def insertCommitteeMembership(membership):
    startInsert, threads, comCount = datetime.now(), [], len(membership)

    threads.extend(getCommitteeMembershipInsertThreads(membership))

    logger.logInfo("Starting insert of", comCount, "committees membership lists with", len(threads), "threads.")
    zjthreads.startThenJoinThreads(threads)
    logger.logInfo("Took",util.seconds_since(startInsert),"seconds to insert", comCount, "committees.")

def doCommitteeMembershipInsert():
    db.deleteRowsFromTables(["Committees"])
    membership = util.getParsedJson(CURRENT_COMMITTEE_LEGISLATORS_URL)
    insertCommitteeMembership(membership)



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
