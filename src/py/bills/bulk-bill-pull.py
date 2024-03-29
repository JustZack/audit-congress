import os, time, shutil, io, json, math
from datetime import datetime

from pprint import pprint

import sys
sys.path.append(os.path.abspath("../"))

from shared import logger, db, zjthreads, util
from bills import billparse as bparse

PROPUBLICA_BULK_BILLS_URL = "https://www.propublica.org/datastore/dataset/congressional-data-bulk-legislation-bills"
BILL_LINKS_SELECTOR = "table li a, div.info-panel div.actions a"

BILLS_DIR = "cache/"

LAST_CONGRESS_PROCESSED = None

SHOW_DEBUG_MESSAGES = True
def debug_print(*strs):
    if SHOW_DEBUG_MESSAGES: print(*strs)

def log(*strs):
    logger.logInfo("bulk-bill", " ".join(str(item) for item in strs))
    if SHOW_DEBUG_MESSAGES: print(*strs)

def logError(*strs):
    logger.logError("bulk-bill", " ".join(str(item) for item in strs))
    if SHOW_DEBUG_MESSAGES: print("ERR:", *strs)

def fetchLastCongress():
    global LAST_CONGRESS_PROCESSED

    sql = "SELECT status FROM CacheStatus where source = 'bulk-bill'"
    result = db.runReturningSql(sql)
    if (len(result) == 1): LAST_CONGRESS_PROCESSED = int(result[0])
    elif (len(result) == 0):
        sql = "INSERT INTO CacheStatus (source, status) VALUES ('bulk-bill', 93)"
        result = db.runCommitingSql(sql)
        LAST_CONGRESS_PROCESSED = 93
    return LAST_CONGRESS_PROCESSED

def scriptAlreadyRunning():
    sql = "SELECT isRunning FROM CacheStatus where source = 'bulk-bill'"
    result = db.runReturningSql(sql)
    if (len(result) == 1): return bool(result[0])
    elif (len(result) == 0):
        sql = "INSERT INTO CacheStatus (source, status, isRunning) VALUES ('bulk-bill', 93, 1)"
        result = db.runCommitingSql(sql)
        return False
    return False

def updateStartingCongress(startingCongress):
    global LAST_CONGRESS_PROCESSED
    LAST_CONGRESS_PROCESSED = startingCongress
    sql = "UPDATE CacheStatus SET status = {} WHERE source = 'bulk-bill'".format(startingCongress)
    db.runCommitingSql(sql)

def updateRunningStatus(isRunning):
    sql = "UPDATE CacheStatus SET isRunning = {} WHERE source = 'bulk-bill'".format(isRunning)
    db.runCommitingSql(sql)





def determineCongressNumberfromPath(url):
    lastSlash = url.rfind("/")
    lastDot = url.rfind(".")
    congress = url[lastSlash+1:lastDot] if lastDot > -1 else url[lastSlash+1:]
    
    return int(congress)

def downloadBillZip(url):
    congress = determineCongressNumberfromPath(url)
    path = BILLS_DIR+str(congress)+".zip"
    util.downloadZipFile(url, path)
    log("Saved bulk bill data for congress", congress, "to", path)

def getBillZipUrls():
    soup = util.getHtmlSoup(PROPUBLICA_BULK_BILLS_URL)
    links = soup.select(BILL_LINKS_SELECTOR)
    return [link["href"] for link in links]

def downloadNeededBillZips():
    urls = [url for url in getBillZipUrls() if determineCongressNumberfromPath(url) >= LAST_CONGRESS_PROCESSED]
    log("started downloading", len(urls), "Zip file{}".format("s" if len(urls) != 1 else ""))

    zjthreads.runThreads(downloadBillZip, urls)

    return len(urls)

def getCachedZipFilePaths():
    zips = sorted(os.listdir(BILLS_DIR), key=lambda z: int(z[:-4]))
    zips = [BILLS_DIR+z for z in zips if z.find(".zip") >= 0 and int(z[:-4]) >= LAST_CONGRESS_PROCESSED]
    return zips

def deleteOutOfDateZips():
    zips = getCachedZipFilePaths()
    zipsToDelete = [z for z in zips if int(z[len(BILLS_DIR):-4]) >= LAST_CONGRESS_PROCESSED]
    util.deleteFiles(zipsToDelete)





def deleteBills(congress=None):
    startDelete = datetime.now()
    toDeleteFrom = ["Bills", "BillSubjects", "BillTitles", "BillCoSponsors"]
    db.deleteRowsFromTables(toDeleteFrom, "congress", congress)
    log("Took", util.seconds_since(startDelete), "seconds to drop", toDeleteFrom, "for congress", congress)

chunkSize = 250
def getAllInsertThreads(bills):
    chunckedBills = util.chunkList(bills, chunkSize)
    threads = []
    for chunk in range(len(chunckedBills)): threads.extend(bparse.getInsertThreads(chunckedBills[chunk]))
    return threads

singleThreadInsert = False
def insertBills(bills):
    startInsert = datetime.now()
    threads = getAllInsertThreads(bills)

    if singleThreadInsert:
        log("Starting insert of",len(bills),"bill data objects sequentially.")
        for thread in threads: zjthreads.startThenJoinThreads([thread])
    else:
        log("Starting",len(threads),"threads to insert",len(bills),"bill data objects in chunks.")
        zjthreads.startThenJoinThreads(threads)
           
    log("Took",util.seconds_since(startInsert),"seconds to insert", len(bills), "bill files")

def parseAndInsertBills(zipFile):
    congress = determineCongressNumberfromPath(zipFile)
    bills, totalRead = [], 0

    deleteThread = zjthreads.buildThread(deleteBills, congress)
    zjthreads.startThreads([deleteThread]) 

    startRead = datetime.now()
    bills = bparse.parseBills(zipFile)
    print("Took {} to read {} bills in congress {}".format(util.seconds_since(startRead), len(bills), congress))
    zjthreads.joinThreads([deleteThread])

    insertBills(bills)
    updateStartingCongress(congress)

fullMultiThreading, threadPooling, poolSize = False, False, 2
def readBillZipFiles():
    zips = getCachedZipFilePaths()

    #Up to 26 Threads Slows everything down
    if fullMultiThreading: threads = zjthreads.runThreads(parseAndInsertBills, zips)
    #Between 2 and 4 threads speeds things up slightly compared to sequential
    elif threadPooling: zjthreads.runThreadPool(parseAndInsertBills, zips, poolSize)
    #No threading seems to have the most consistent performance though
    else: [parseAndInsertBills(zipFile) for zipFile in zips]
    #The following represent each different file format
    #if zipFile.find("93") >= 0 or zipFile.find("113") >= 0 or zipFile.find("117") >= 0:
    #if zipFile.find("117") >= 0:
    #if zipFile.find("113") >= 0:
    #if zipFile.find("93") >= 0:
    #    readBillZip(zipFile)

def stopWithError(error):
    logError(error)
    updateRunningStatus(False)

def doSetup():
    #Make sure the DB schema is valid first
    if db.schemaIsValid(): log("Confirmed DB Schema is valid via the API.")
    else: raise Exception("Could not validate the DB schema via API. Exiting.")
    
    #Fetch the ThomasID => BioguideId mapping
    if bparse.fetchMemberMapping(): log("Found {} thomas_id -> bioguide_id mappings via the API".format(len(bparse.MEMBERS_MAPPING)))
    else: raise Exception("Could not fetch thomas_id -> bioguide_id mapping from API")

#~2800s to run with 16MB cache (With Truncate)
#~1550s to run with 2048MB cache (With Truncate)
#~1500s to run with 4096MB cache (With Truncate)
def doBulkBillPull():
    #Make sure the script isnt already running according to the DB
    if not scriptAlreadyRunning(): updateRunningStatus(True)
    else: raise Exception("Tried running script when it is already running! Exiting.")

    #State where the process is starting, based off the database
    log("Starting fetch, parse, and insert at congress", fetchLastCongress())

    #If the cache exists, ensure old data is deleted (based on CacheStatus table)
    if os.path.exists(BILLS_DIR): deleteOutOfDateZips()

    #Then rebuild the needed cache items
    startDownload = datetime.now()
    count = downloadNeededBillZips()
    log("Took", util.seconds_since(startDownload),"seconds to download",count,"zip file{}".format("s" if count != 1 else ""))

    #Track how long it takes to parse and insert the bills
    startInsert = datetime.now()
    readBillZipFiles()
    timeToInsert = util.seconds_since(startInsert)
    
    #Count rows in each updated table
    billCount = db.countRows("Bills")
    subjectCount = db.countRows("BillSubjects")
    titlesCount = db.countRows("BillTitles")
    cosponCount = db.countRows("BillCoSponsors")

    #Final log of what happened
    log("Took", timeToInsert,"seconds to parse & insert",billCount,"bills,",subjectCount,"subjects,",titlesCount,"titles, and",cosponCount,"cosponsors.")

    #Mark this script as done running in the database
    updateRunningStatus(False)

if __name__ == "__main__":   
    try:
        doSetup()
        doBulkBillPull()
    except KeyboardInterrupt: stopWithError("Manually ended script via ctrl+c")
    except Exception as e: stopWithError("Stopped with Exception: {}".format(e))
