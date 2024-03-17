import os, time, shutil, io, json, math
from zipfile import ZipFile
from datetime import datetime

import xmltodict
from pprint import pprint

import requests as rq
from bs4 import BeautifulSoup

import sys
sys.path.append(os.path.abspath("../"))

from shared import logger, db, zjthreads

PROPUBLICA_BULK_BILLS_URL = "https://www.propublica.org/datastore/dataset/congressional-data-bulk-legislation-bills"
BILL_LINKS_SELECTOR = "table li a, div.info-panel div.actions a"

BILLS_DIR = "cache/"
#All folders that we care about in the bills folder
BILL_TYPE_FOLDERS = ["hr", "hconres", "hjres", "hres", "s" , "sconres", "sjres", "sres"]

MEMBERS_MAPPING_API_URL = "http://localhost/audit-congress/src/api/api.php?route=bioguideToThomas"
MEMBERS_MAPPING = None

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
    
def seconds_since(a): return (datetime.now()-a).total_seconds()
def countFiles(inDir):
    count = 0
    for root_dir, cur_dir, files in os.walk(inDir): count += len(files)
    return count

def ensureFoldersExist(path): os.makedirs(os.path.dirname(path), exist_ok=True)

def saveFileAny(writeType, path, data):
    ensureFoldersExist(path)
    file = open(path, writeType)
    file.write(data)
    file.close()
def saveBinaryFile(path, data): saveFileAny("wb", path, data)
def saveFile(path, data): saveFileAny("w", path, data)

def downloadZipFile(url, savePath): saveBinaryFile(savePath+".zip", rq.get(url).content)

def getParsedSoup(url, features="html.parser"):
    page = rq.get(url)
    log("GET:", url)
    soup = BeautifulSoup(page.content, features)
    return soup
def getParsedHtml(url): return getParsedSoup(url)
def getParsedXml(url): return getParsedSoup(url, "xml")

def chunkList(array, chunkSize):
    chunckedList = []
    numChunks = math.ceil(len(array)/chunkSize)
    for n in range(numChunks):
        start = n*chunkSize
        end = (n+1)*chunkSize
        chunckedList.append(array[start:end])
    return chunckedList

def countRows(inTable, congress=None): 
    sql = ""
    if congress is None: sql = "SELECT COUNT(*) FROM {}".format(inTable)
    else: sql = "SELECT COUNT(*) FROM {} WHERE congress = {}".format(inTable, congress)

    count =   db.runReturningSql(sql)[0]
    return count

threadedDelete = False
def deleteBills(congress=None):
    startDelete = datetime.now()
    toDeleteFrom = ["Bills", "BillSubjects", "BillTitles", "BillCoSponsors"]

    queries = []
    sql = ""
    for table in toDeleteFrom:
        if congress is None: sql = "TRUNCATE {}".format(table)
        else: sql = "DELETE FROM {} WHERE congress = {}".format(table, congress)
        queries.append(sql)

    if threadedDelete: zjthreads.runThreads(db.runCommitingSql, queries)
    else: [db.runCommitingSql(sql) for sql in queries]

    log("Took", seconds_since(startDelete), "seconds to drop", toDeleteFrom, "for congress", congress)

def getMemberByThomasId(thomasId):
    global MEMBERS_MAPPING
    try: return MEMBERS_MAPPING[thomasId]
    except Exception as e: return None

def fetchMemberMapping():
    global MEMBERS_MAPPING
    page = rq.get(MEMBERS_MAPPING_API_URL)
    resp = json.loads(page.content)
    if "mapping" in resp:
        MEMBERS_MAPPING =  resp["mapping"]
        return True
    else:
        return False

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



def getBillFolderDict(fileList):
    folders = dict()
    for file in fileList:
        lastDot = file.rfind(".")
        lastSlash = file.rfind("/")+1
        
        directory = file[0:lastSlash]
        file = file[lastSlash:]

        if directory not in folders: folders[directory] = set()
        
        #implies this is a file
        if lastDot > lastSlash: folders[directory].add(file)
    return folders

def determineCongressNumberfromPath(url):
    lastSlash = url.rfind("/")
    lastDot = url.rfind(".")
    congress = url[lastSlash+1:lastDot] if lastDot > -1 else url[lastSlash+1:]
    
    return int(congress)

def downloadBillZip(url):
    congress = determineCongressNumberfromPath(url)
    savePath = BILLS_DIR+str(congress)
    downloadZipFile(url, savePath)
    log("Saved bulk bill data for congress", congress, "to", savePath)

def getBillZipUrls():
    soup = getParsedHtml(PROPUBLICA_BULK_BILLS_URL)
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
    for z in zipsToDelete: 
        log("Delete:",z)
        os.remove(z)




def getKeyIfSet(dictionary, defaultValue, *keys):
    item = dictionary
    if item is not None:
        for key in keys:
            if key in item: item = item[key]
            if item is None: break
            
    return defaultValue

def parseBillFDSYSXml(fileData):
    xmlData = xmltodict.parse(fileData)
    
    bill = xmlData["billStatus"]["bill"] 

    billData = dict()

    typ = bill["type"] if "type" in bill.keys() else bill["billType"]
    cong = bill["congress"]
    num = bill["number"] if "number" in bill.keys() else bill["billNumber"]

    actualBill = dict()
    billData["bill"] = actualBill

    actualBill["type"] = typ
    actualBill["congress"] = cong
    actualBill["number"] = num

    sponsor = bill["sponsors"]
    actualBill["bioguideId"] = sponsor["item"]["bioguideId"] if sponsor is not None else ""

    actualBill["title"] = bill["title"]
    
    actualBill["introduced_at"] = bill["introducedDate"]
    actualBill["updated_at"] = bill["updateDate"]

    actualBill["originChamber"] = bill["originChamber"]

    policyArea = bill["policyArea"] if "policyArea" in bill else None
    actualBill["policyArea"] = policyArea["name"] if policyArea is not None else ""
    actualBill["summaries"] = bill["summaries"] if "summaries" in bill else []
    
    subjects = bill["subjects"] if "subjects" in bill else None
    ["subjects", "billSubjects", "legislativeSubjects", "item"]
    if subjects is not None:
        if "billSubjects" in subjects: subjects = subjects["billSubjects"]
        subjects = subjects["legislativeSubjects"]
        if subjects is not None: subjects = subjects["item"]
        else: subjects = []
        
        if type(subjects) is dict: subjects = [subjects]
    else: subjects = []

    cosponsoredDat = bill["cosponsors"] if "cosponsors" in bill else None
    ["cosponsors", "item"]
    if cosponsoredDat is not None: cosponsoredDat = cosponsoredDat["item"]
    else: cosponsoredDat = []

    cosponsored = []
    if type(cosponsoredDat) is list:
        for cospon in cosponsoredDat:
            id = cospon["bioguideId"] if "bioguideId" in cospon else getMemberByThomasId(cospon["thomas_id"])
            since = cospon["sponsorshipDate"]
            withdrawn = cospon["sponsorshipWithdrawnDate"] if "sponsorshipWithdrawnDate" in cospon else None
            isOriginal = cospon["isOriginalCosponsor"] if "isOriginalCosponsor" in cospon else None
            cosponsored.append({"id": id, "sponsoredAt": since, "withdrawnAt": withdrawn, "isOriginal": isOriginal})
    elif type(cosponsoredDat) is dict:
        id = cosponsoredDat["bioguideId"] if "bioguideId" in cosponsoredDat else getMemberByThomasId(cosponsoredDat["thomas_id"])
        since = cosponsoredDat["sponsorshipDate"]
        withdrawn = cosponsoredDat["sponsorshipWithdrawnDate"] if "sponsorshipWithdrawnDate" in cosponsoredDat else None
        isOriginal = cosponsoredDat["isOriginalCosponsor"] if "isOriginalCosponsor" in cosponsoredDat else None
        cosponsored.append({"id": id, "sponsoredAt": since, "withdrawnAt": withdrawn, "isOriginal": isOriginal})


    committees = bill["committees"] if "committees" in bill else None
    ["billCommittees", "item"]
    if committees is not None:
        if "billCommittees" in committees: committees = committees["billCommittees"]
        if committees is not None and "item" in committees: committees = committees["item"]
        else: committees = []
    else: committees = []

    amendments = bill["amendments"] if "amendments" in bill else None
    ["amendments", "amendment"]
    if amendments is not None:
        if "amendment" in amendments: amendments = amendments["amendment"]
        else: amendments = []
    else: amendments = []

    actions = bill["actions"] if "actions" in bill else None
    if actions is not None:
        if "item" in actions: actions = actions["item"]
        else: actions = []
    else: actions = []

    laws = bill["laws"] if "laws" in bill else None
    if laws is not None:
        if "item" in laws: laws = laws["item"]
        else: laws = []
    else: laws = []

    titles = bill["titles"] if "titles" in bill else None
    if titles is not None:
        if "item" in titles: titles = titles["item"]
        else: titles = []
    else: titles = []

    titles = [{"type": title["titleType"], "title": title["title"], "as": "", "is_for_portion": ""} for title in titles]

    billData["bill"] = actualBill
    billData["titles"] = titles
    billData["subjects"] = [subject["name"] for subject in subjects]
    billData["cosponsors"] = cosponsored
    billData["committees"] = committees
    billData["amendments"] = amendments
    billData["actions"] = actions
    billData["laws"] = laws

    return billData

def parseBillDataXml(fileData):
    raise Exception("data.xml is not implemented")

    billData = dict()
    xmlData = xmltodict.parse(fileData)
    
    actualBill = dict()
    actualBill["id"] = jsonData["bill_id"]

    billElem = xmlData.select("<bill>")
    print(billElem)
    actualBill["type"] = billElem["type"]
    actualBill["congress"] = billElem["session"]
    actualBill["number"] = billElem["number"]
    return billData
    sponsor = jsonData["sponsor"]
    if (sponsor is not None):
        if "bioguide_id" in sponsor:
            actualBill["bioguideId"] = sponsor["bioguide_id"]
        else:
            actualBill["bioguideId"] = getMemberByThomasId(sponsor["thomas_id"])
    else:
        actualBill["bioguideId"] = None

    actualBill["officialTitle"] = jsonData["official_title"]
    actualBill["popularTitle"] = jsonData["popular_title"]

    billData["bill"] = actualBill
    billData["titles"] = jsonData["titles"]
    billData["subjects"] = jsonData["subjects"]
    billData["cosponsors"] = jsonData["cosponsors"]
    billData["committees"] = jsonData["committees"]
    billData["amendments"] = jsonData["amendments"]
    billData["actions"] = jsonData["actions"]

    return billData

def parseBillDataJson(fileData):
    billData = dict()
    jsonData = json.loads(fileData)

    actualBill = dict()
    
    actualBill["type"] = jsonData["bill_type"]
    actualBill["congress"] = jsonData["congress"]
    actualBill["number"] = jsonData["number"]

    sponsor = jsonData["sponsor"]
    if (sponsor is not None):
        if "bioguide_id" in sponsor:
            actualBill["bioguideId"] = sponsor["bioguide_id"]
        else:
            actualBill["bioguideId"] = getMemberByThomasId(sponsor["thomas_id"])
    else:
        actualBill["bioguideId"] = None

    actualBill["title"] = jsonData["official_title"]

    actualBill["introduced_at"] = jsonData["introduced_at"]
    actualBill["updated_at"] = jsonData["updated_at"]

    cosponsors = []
    for cospon in jsonData["cosponsors"]:
        id = cospon["bioguide_id"] if "bioguide_id" in cospon else getMemberByThomasId(cospon["thomas_id"])
        since = cospon["sponsored_at"]
        withdrawn = cospon["withdrawn_at"] if "withdrawn_at" in cospon else None
        isOriginal = None
        cosponsors.append({"id": id, "sponsoredAt": since, "withdrawnAt": withdrawn, "isOriginal": isOriginal})
    
    billData["bill"] = actualBill
    billData["titles"] = jsonData["titles"]
    billData["subjects"] = jsonData["subjects"]
    billData["cosponsors"] = cosponsors
    billData["committees"] = jsonData["committees"]
    billData["amendments"] = jsonData["amendments"]
    billData["actions"] = jsonData["actions"]
    billData["actions"] = jsonData["actions"]

    return billData

def getInsertThreads(bills):
    billSql = "INSERT INTO Bills (id, type, congress, number, bioguideId, title, introduced, updated) "\
              "VALUES (%s, %s, %s, %s, %s, %s, %s, %s)"
    subjectSql = "INSERT INTO BillSubjects (id, type, congress, number, subjectIndex, subject) "\
                 "VALUES (%s, %s, %s, %s, %s, %s)"
    titleSql = "INSERT INTO BillTitles (id, type, congress, number, titleIndex, title, titleType, titleAs, isForPortion) "\
               "VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)"
    coSponSql = "INSERT INTO BillCoSponsors (id, type, congress, number, bioguideId, sponsoredAt, withdrawnAt, isOriginal) "\
                "VALUES (%s, %s, %s, %s, %s, %s, %s, %s)"
    
    billData,subjectData,titleData,cosponData = [],[],[],[]

    for parsedBill in bills:
        bill = parsedBill["bill"]
        t,c,n = bill["type"].lower(),bill["congress"],bill["number"]
        bioguide = bill["bioguideId"]

        id = "{}{}-{}".format(t, n, c)

        billData.append((id, t, c, n, bioguide, bill["title"], 
                         bill["introduced_at"], bill["updated_at"]))
        
        pid = id+"-{}"
        i = 0        
        for subject in parsedBill["subjects"]: 
            sid = pid.format(i)
            subjectData.append((sid, t, c, n, i, subject))
            i += 1

        i = 0
        for title in parsedBill["titles"]: 
            tid = pid.format(i)
            isForPortion = title["is_for_portion"] if "is_for_portion" in title.keys() else ""
            titleData.append((tid, t, c, n, i, title["title"], title["type"], title["as"], isForPortion))
            i += 1
        
        i = 0
        for cosponsor in parsedBill["cosponsors"]:
            cid = pid.format(cosponsor["id"])+"-"+str(i)
            cosponData.append((cid, t, c, n, cosponsor["id"], cosponsor["sponsoredAt"], cosponsor["withdrawnAt"], cosponsor["isOriginal"]))
            i += 1

    threads = []
    threads.append(zjthreads.buildThread(db.runInsertingSql, billSql, billData))
    threads.append(zjthreads.buildThread(db.runInsertingSql, subjectSql, subjectData))
    threads.append(zjthreads.buildThread(db.runInsertingSql, titleSql, titleData))
    threads.append(zjthreads.buildThread(db.runInsertingSql, coSponSql, cosponData))
    return threads

def readZippedFiles(zipFile):
    bills = []
    files = zipFile.namelist()
    folderDict = getBillFolderDict(files)
    totalFilesRead = 0
    skippedFiles = 0

    for name,folder in folderDict.items():
        if name.find("amendments") >= 0: continue

        file = None
        if "data.json" in folder: file = name+"data.json"
        elif "data.xml" in folder: file = name+"data.xml"
        elif "fdsys_billstatus.xml" in folder: file = name+"fdsys_billstatus.xml"

        if file is not None:
            data = zipFile.read(file)
            bill = None
            if file.find("data.json") >= 0: bill = parseBillDataJson(data)
            #elif file.find("data.xml") >= 0: bill = parseBillDataXml(data)
            elif file.find("fdsys_billstatus.xml") >= 0: bill = parseBillFDSYSXml(data)

            if bill is not None: 
                    totalFilesRead += 1
                    bills.append(bill)
            else: skippedFiles += 1

    if skippedFiles > 0: print("Skipped",skippedFiles,"fdsys_billstatus.xml files")
    return bills

singleThreadInsert = False
threadPoolInsert = False
threadPoolSize = 100
chunkSize = 250
def readBillZip(filename):   
    congress = determineCongressNumberfromPath(filename)
    bills, totalRead = [], 0

    deleteThread = zjthreads.buildThread(deleteBills, congress)
    zjthreads.startThreads([deleteThread]) 

    with ZipFile(filename, 'r') as zipped: bills = readZippedFiles(zipped)
    chunckedBills = chunkList(bills, chunkSize)

    zjthreads.joinThreads([deleteThread])
    startInsert = datetime.now()
    if singleThreadInsert:
        log("Starting insert of",len(bills),"bill data objects in",len(chunckedBills),"chunks.")
        for chunk in range(len(chunckedBills)):
            zjthreads.startThenJoinThreads(getInsertThreads(chunckedBills[chunk]))
    elif not threadPoolInsert:
            threads = []
            for chunk in range(len(chunckedBills)):
                inserts = getInsertThreads(chunckedBills[chunk])
                threads.extend(inserts)
            log("Starting",len(threads),"threads to insert",len(bills),"bill data objects in",len(chunckedBills),"chunks.")
            zjthreads.startThenJoinThreads(threads)
    else:
        log("Starting ThreadPool({}) to insert".format(threadPoolSize),len(bills),"bill data objects in",len(chunckedBills),"chunks.")
        #zjthreads.runThreadPool(getInsertThreads, chunckedBills, threadPoolSize)
        #with ThreadPoolExecutor(threadPoolSize) as exec:
        #    for chunk in range(len(chunckedBills)):
        #            exec.submit(insertBills, chunckedBills[chunk])
           
    updateStartingCongress(congress)
    log("Took",seconds_since(startInsert),"seconds to insert", len(bills), "bill files from", filename)
    time.sleep(2)

fullMultiThreading, threadPooling, poolSize, noThreading = False, False, 2, True
def readBillZipFiles():
    zips = getCachedZipFilePaths()

    log("Started parsing", len(zips), "Zip file{}".format("s" if len(zips) != 1 else ""))

    #Up to 26 Threads Slows everything down
    if fullMultiThreading:
        threads = zjthreads.runThreads(readBillZip, zips)
    #Between 2 and 4 threads speeds things up slightly compared to sequential
    elif threadPooling:
        zjthreads.runThreadPool(readBillZip, zips, poolSize)
    #No threading seems to have the most consistent performance though
    elif noThreading:
        for zipFile in zips:
            readBillZip(zipFile)
            #The following represent each different file format
            #if zipFile.find("93") >= 0 or zipFile.find("113") >= 0 or zipFile.find("117") >= 0:
            #if zipFile.find("117") >= 0:
            #if zipFile.find("113") >= 0:
            #if zipFile.find("93") >= 0:
            #    readBillZip(zipFile)

def stopWithError(error):
    logError(error)
    updateRunningStatus(False)

#~2800s to run with 16MB cache (With Truncate)
#~1550s to run with 2048MB cache (With Truncate)
#~1500s to run with 4096MB cache (With Truncate)
def doBulkBillPull():
    #Make sure the DB schema is valid first
    if not db.schemaIsValid(): 
        logError("Could not validate the DB schema via API. Exiting.")
        return
    else: log("Confirmed DB Schema is valid via the API.")
    
    #Make sure the script isnt already running according to the DB
    if scriptAlreadyRunning(): 
        logError("Tried running script when it is already running! Exiting.")
        return
    else: updateRunningStatus(True)

    #Fetch the ThomasID => BioguideId mapping
    if not fetchMemberMapping(): raise Exception("Could not fetch thomas_id -> bioguide_id mapping from API")
    else: log("Found",len(MEMBERS_MAPPING),"thomas_id -> bioguide_id mappings via the API")
    
    #State where the process is starting, based off the database
    log("Starting fetch, parse, and insert at congress", fetchLastCongress())

    #If the cache exists, ensure old data is deleted
    if os.path.exists(BILLS_DIR): deleteOutOfDateZips()

    #Then rebuild the needed cache items
    startDownload = datetime.now()
    count = downloadNeededBillZips()
    log("Took", seconds_since(startDownload),"seconds to download",count,"zip file{}".format("s" if count != 1 else ""))

    #Track how long it takes to parse and insert the bills
    startInsert = datetime.now()
    readBillZipFiles()
    timeToInsert = seconds_since(startInsert)
    
    #Count rows in each updated table
    billCount = countRows("Bills")
    subjectCount = countRows("BillSubjects")
    titlesCount = countRows("BillTitles")
    cosponCount = countRows("BillCoSponsors")

    #Final log of what happened
    log("Took", timeToInsert,"seconds to parse & insert",billCount,"bills,",subjectCount,"subjects,",titlesCount,"titles, and",cosponCount,"cosponsors.")

    #Mark this script as done running in the database
    updateRunningStatus(False)

if __name__ == "__main__":   
    try:
        doBulkBillPull()
    except KeyboardInterrupt: 
        stopWithError("Manually ended script via ctrl+c")
    except Exception as e: 
        stopWithError("Stopped with Exception {}".format(e))
