import os, time, shutil, io, json, math
from datetime import datetime

from pprint import pprint

import sys
sys.path.append(os.path.abspath("../"))

from shared import logger, db, zjthreads, util

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
SOCIAL_COLUMNS = []
OFFICES_COLUMNS = []
#Works best with chunk size > len(currentMembers) and size <= 1000
MEMBER_CHUNK_SIZE = 1000

def appendMemberUpdateTimes(row):
    now = time.time()
    row.append(now)
    row.append(now + (60*60*24*7))
    return row



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
    db.deleteRowsFromTables(["Members", "MemberTerms", "MemberElections", "MemberSocials", "MemberOffices"])
    zjthreads.runThreads(doMemberInsertGroup, [True, False])



def doSetup(): util.genericBulkScriptSetup(SCRIPT_NAME)

def doBulkMemberPull():
    threads = []

    threads.append(zjthreads.buildThread(doMemberInsert))

    zjthreads.startThenJoinThreads(threads)

def main(): util.genericBulkScriptMain(doSetup, doBulkMemberPull, SCRIPT_NAME)

if __name__ == "__main__": util.runAndCatchMain(main, util.updateScriptRunningStatus, SCRIPT_NAME, False)
