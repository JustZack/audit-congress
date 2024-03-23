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
CURRENT_COMMITTEE_LEGISLATORS_URL = "{}committee-membership-current.json".format(BASE_URL)
HISTORICAL_COMMITTEES_URL = "{}committees-historical.json".format(BASE_URL)

MEMBER_COLUMNS = ["bioguideId", "thomasId", "lisId", "govTrackId", "openSecretsId", "voteSmartId",
                  "cspanId", "mapLightId", "icpsrId", "wikidataId", "googleEntityId",
                  "official_full", "first", "last", "gender", "birthday", "isCurrent",
                  "lastUpdate", "nextUpdate"]
TERM_COLUMNS = ["bioguideId", "type", "start", "end", "state", "district", "party", "class", "how",
                "state_rank", "url", "rss_url", "contact_form", "address", "office", "phone",
                "lastUpdate", "nextUpdate"]
ELECTION_COLUMNS = ["fecId", "bioguideId", "lastUpdate", "nextUpdate"]

def getFieldIfExists(theDict, theField):
    return theDict[theField] if theField in theDict else ""

def appendMemberUpdateTimes(row):
    now = time.time()
    row.append(now)
    row.append(now + (60*60*24*7))
    return row

def getMemberRow(member, isCurrent):
    mRow = []
    mId, mName, mBio = member["id"], member["name"], member["bio"]
    mRow.append(mId["bioguide"])
    mRow.append(getFieldIfExists(mId, "thomas"))
    mRow.append(getFieldIfExists(mId, "lis"))
    mRow.append(getFieldIfExists(mId, "govtrack"))
    mRow.append(getFieldIfExists(mId, "opensecrets"))
    mRow.append(getFieldIfExists(mId, "votesmart"))
    mRow.append(getFieldIfExists(mId, "cspan"))
    mRow.append(getFieldIfExists(mId, "maplight"))
    mRow.append(getFieldIfExists(mId, "icpsr"))
    mRow.append(getFieldIfExists(mId, "wikidata"))
    mRow.append(getFieldIfExists(mId, "google_entity_id"))

    mRow.append(getFieldIfExists(mName, "official_full"))
    mRow.append(getFieldIfExists(mName, "first"))
    mRow.append(getFieldIfExists(mName, "last"))

    mRow.append(getFieldIfExists(mBio, "gender"))
    mRow.append(getFieldIfExists(mBio, "birthday"))

    mRow.append(isCurrent)
    
    return appendMemberUpdateTimes(mRow)

def getTermRows(terms, bioguideId):
    mTerms = []
    for term in terms:
        mTerm = []
        mTerm.append(bioguideId)
        mTerm.append(getFieldIfExists(term, "type"))
        mTerm.append(getFieldIfExists(term, "start"))
        mTerm.append(getFieldIfExists(term, "end"))
        mTerm.append(getFieldIfExists(term, "state"))
        mTerm.append(getFieldIfExists(term, "district"))
        mTerm.append(getFieldIfExists(term, "party"))
        mTerm.append(getFieldIfExists(term, "class"))
        mTerm.append(getFieldIfExists(term, "how"))
        mTerm.append(getFieldIfExists(term, "state_rank"))
        mTerm.append(getFieldIfExists(term, "url"))
        mTerm.append(getFieldIfExists(term, "rss_url"))
        mTerm.append(getFieldIfExists(term, "contact_form"))
        mTerm.append(getFieldIfExists(term, "address"))
        mTerm.append(getFieldIfExists(term, "office"))
        mTerm.append(getFieldIfExists(term, "phone"))

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

chunkSize = 1000
def parseAndInsertMembers():
    db.deleteRowsFromTables(["Members", "MemberTerms", "MemberElections"])

    current = util.getParsedJson(CURRENT_LEGISLATORS_URL)
    historical = util.getParsedJson(HISTORICAL_LEGISLATORS_URL)

    chunkedCurrent = util.chunkList(current, chunkSize)
    chunkedHistorical = util.chunkList(historical, chunkSize)
    startInsert = datetime.now()

    threads = []
    threads.extend(getChunkedMemberInsertThreads(chunkedCurrent, True))
    threads.extend(getChunkedMemberInsertThreads(chunkedHistorical, False))

    zjthreads.startThenJoinThreads(threads)
    logger.logInfo("Took",util.seconds_since(startInsert),"seconds to insert", len(current)+len(historical), "members.")
    

def doSetup():
    logger.setLogAction(SCRIPT_NAME)

    #Make sure the DB schema is valid first
    db.throwIfShemaInvalid()

def doBulkMemberPull():
    parseAndInsertMembers()
    #mems = util.getParsedJson(LEGISLATORS_SOCIALS_URL)
    #memo = util.getParsedJson(LEGISLATORS_OFFICES_URL)
    #print("found", len(memc))
    #print("found", len(memh))
    #print("found", len(mems))
    #print("found", len(memo))
    return

def main():
    doSetup()

    util.throwIfScriptAlreadyRunning(SCRIPT_NAME)

    util.updateScriptRunningStatus(SCRIPT_NAME, True)
    doBulkMemberPull()
    util.updateScriptRunningStatus(SCRIPT_NAME, False)

if __name__ == "__main__": util.runAndCatchMain(main, util.updateScriptRunningStatus, SCRIPT_NAME, False)
