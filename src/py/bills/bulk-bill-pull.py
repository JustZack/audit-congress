import os, time, shutil, io, json, math
from datetime import datetime

from pprint import pprint

import sys
sys.path.append(os.path.abspath("../"))

from shared import logger, db, zjthreads, util, cache
from bills import billparse as bparse

SCRIPT_NAME = "bulk-bill"

PROPUBLICA_BULK_BILLS_URL = "https://www.propublica.org/datastore/dataset/congressional-data-bulk-legislation-bills"
BILL_LINKS_SELECTOR = "table li a, div.info-panel div.actions a"

ZIP_CACHE_DIR = "zip/"
CSV_CACHE_DIR = "csv/"

LAST_CONGRESS_PROCESSED = None

REFRESH_BILL_ZIPS = True

def fetchLastCongress():
    global LAST_CONGRESS_PROCESSED

    sql = "SELECT status FROM CacheStatus where source = 'bulk-bill'"
    result = db.runReturningSql(sql)
    if (len(result) == 1): 
        res = result[0][0]
        if res != None and str.isdigit(res): LAST_CONGRESS_PROCESSED = int(res)
        else: LAST_CONGRESS_PROCESSED = 93
    elif (len(result) == 0):
        sql = "INSERT INTO CacheStatus (source, status) VALUES ('bulk-bill', 93)"
        result = db.runCommitingSql(sql)
        LAST_CONGRESS_PROCESSED = 93
    return LAST_CONGRESS_PROCESSED

def updateStartingCongress(startingCongress):
    global LAST_CONGRESS_PROCESSED
    LAST_CONGRESS_PROCESSED = startingCongress
    sql = "UPDATE CacheStatus SET status = {} WHERE source = 'bulk-bill'".format(startingCongress)
    db.runCommitingSql(sql)





def determineCongressNumberfromPath(url):
    lastSlash = url.rfind("/")
    lastDot = url.rfind(".")
    congress = url[lastSlash+1:lastDot] if lastDot > -1 else url[lastSlash+1:]
    
    return int(congress)

def downloadBillZip(url):
    congress = determineCongressNumberfromPath(url)
    path = ZIP_CACHE_DIR+str(congress)+".zip"
    util.downloadZipFile(url, path)
    logger.logInfo("Saved bulk bill data for congress", congress, "to", path)

def getBillZipUrls():
    soup = util.getHtmlSoup(PROPUBLICA_BULK_BILLS_URL)
    links = soup.select(BILL_LINKS_SELECTOR)
    return [link["href"] for link in links]

def downloadNeededBillZips():
    urls = [url for url in getBillZipUrls() if determineCongressNumberfromPath(url) >= LAST_CONGRESS_PROCESSED]
    logger.logInfo("started downloading", len(urls), "Zip file{}".format("s" if len(urls) != 1 else ""))

    zjthreads.runThreads(downloadBillZip, urls)

    return len(urls)

def getCachedZipFilePaths():
    zips = sorted(os.listdir(ZIP_CACHE_DIR), key=lambda z: int(z[:-4]))
    zips = [ZIP_CACHE_DIR+z for z in zips if z.find(".zip") >= 0 and int(z[:-4]) >= LAST_CONGRESS_PROCESSED]
    return zips

def deleteOutOfDateZips():
    zips = getCachedZipFilePaths()
    zipsToDelete = [z for z in zips if int(z[len(ZIP_CACHE_DIR):-4]) >= LAST_CONGRESS_PROCESSED]
    util.deleteFiles(zipsToDelete)





def deleteBills(congress=None):
    startDelete = datetime.now()
    toDeleteFrom = ["Bills", "BillSubjects", "BillTitles", "BillCoSponsors"]
    db.deleteRowsFromTables(toDeleteFrom, "congress", congress)
    logger.logInfo("Took", util.seconds_since(startDelete), "seconds to drop", toDeleteFrom, "for congress", congress)

chunkSize = 250
def getAllInsertThreads(bills):
    chunckedBills = util.chunkList(bills, chunkSize)
    threads = []
    for chunk in range(len(chunckedBills)): threads.extend(bparse.getInsertThreads(chunckedBills[chunk]))
    return threads


singleThreadInsert = False
def insertBillsWithInsert(bills):
    startInsert = datetime.now()
    threads = getAllInsertThreads(bills)

    if singleThreadInsert:
        logger.logInfo("Starting insert of",len(bills),"bill data objects sequentially.")
        for thread in threads: zjthreads.startThenJoinThreads([thread])
    else:
        logger.logInfo("Starting",len(threads),"threads to insert",len(bills),"bill data objects.")
        zjthreads.startThenJoinThreads(threads)
           
    logger.logInfo("Took",util.seconds_since(startInsert),"seconds to insert", len(bills), "bill files")

def insertBillsWithBulkLoad(bills, congress):
    startInsert = datetime.now()
    tableRows = bparse.splitBillsIntoTableRows(bills)
    path = util.relativeToAbsPath(CSV_CACHE_DIR+"{}-{}.csv")
    for name in tableRows.keys():
        data = tableRows[name]
        if len(data) == 0: 
            logger.logInfo("No data to insert for", name,"in congress",congress)
            continue
        filePath = path.format(name, congress)
        logger.logInfo("Bulk inserting", len(data),"into",name,"for congress",congress)
        util.saveAsCSV(filePath, tableRows[name])
        db.loadDataInFile(name, filePath)
    logger.logInfo("Took",util.seconds_since(startInsert),"seconds to insert", len(bills), "bill files for congress", congress)

def parseAndInsertBills(zipFile):
    congress = determineCongressNumberfromPath(zipFile)
    bills, totalRead = [], 0

    startRead = datetime.now()
    bills = bparse.parseBills(zipFile)
    logger.logInfo("Took {} to read {} bills in congress {}".format(util.seconds_since(startRead), len(bills), congress))
    
    insertBillsWithBulkLoad(bills, congress)
    #insertBillsWithInsert(bills)
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

def doSetup():
    util.genericBulkScriptSetup(SCRIPT_NAME)
    
    #Fetch the ThomasID => BioguideId mapping
    if bparse.fetchMemberMapping(): logger.logInfo("Found {} thomas_id -> bioguide_id mappings via the API".format(len(bparse.MEMBERS_MAPPING)))
    else: raise Exception("Could not fetch thomas_id -> bioguide_id mapping from API")



def getUpdatedZips():
    #If the cache exists, ensure old data is deleted (based on CacheStatus table)
    if os.path.exists(ZIP_CACHE_DIR): deleteOutOfDateZips()

    #Then rebuild the needed cache items
    startDownload = datetime.now()
    count = downloadNeededBillZips()
    logger.logInfo("Took", util.seconds_since(startDownload),"seconds to download",count,"zip file{}".format("s" if count != 1 else ""))

#~2800s to run with 16MB cache (With Truncate)
#~1550s to run with 2048MB cache (With Truncate)
#~1500s to run with 4096MB cache (With Truncate)
def doBulkBillPull():
    startRun = datetime.now()
    #State where the process is starting, based off the database
    logger.logInfo("Starting fetch, parse, and insert at congress", fetchLastCongress())

    #Ensure the bulk zip files are up to date (based on the last zip file fetched)
    #Note that any zip files not coming before the current congress are considered old (as determined by api.congress.gov)
    if REFRESH_BILL_ZIPS: getUpdatedZips()

    #Track how long it takes to parse and insert the bills
    startInsert = datetime.now()
    readBillZipFiles()
    timeToInsert = util.seconds_since(startInsert)
    
    #Final log of what happened
    logger.logInfo("Took", util.seconds_since(startRun),"seconds to download, parse & insert bill files.")

def main(): util.genericBulkScriptMain(doSetup, doBulkBillPull)

if __name__ == "__main__": 
    util.runAndCatchMain(main)
