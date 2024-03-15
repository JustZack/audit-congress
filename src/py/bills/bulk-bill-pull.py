import os, time, shutil, io, json
from zipfile import ZipFile
from datetime import datetime

import xmltodict
from pprint import pprint

import threading
from concurrent.futures import ThreadPoolExecutor

import requests as rq
from bs4 import BeautifulSoup
import mysql.connector

PROPUBLICA_BULK_BILLS_URL = "https://www.propublica.org/datastore/dataset/congressional-data-bulk-legislation-bills"
BILL_LINKS_SELECTOR = "table li a, div.info-panel div.actions a"

BILLS_DIR = "cache/"

MEMBERS_MAPPING_API_URL = "http://localhost/audit-congress/src/api/api.php?route=bioguideToThomas"
MEMBERS_MAPPING = None
#All folders that we care about in the bills folder
BILL_TYPE_FOLDERS = ["hr", "hconres", "hjres", "hres", "s" , "sconres", "sjres", "sres"]

SHOW_DEBUG_MESSAGES = False
def debug_print(*strs):
    if SHOW_DEBUG_MESSAGES: print(*strs)

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
    debug_print("GET:", url)
    soup = BeautifulSoup(page.content, features)
    return soup
def getParsedHtml(url): return getParsedSoup(url)
def getParsedXml(url): return getParsedSoup(url, "xml")



# Opens a connection with a MySQL host
def mysql_connect():
    return mysql.connector.connect(host="127.0.0.1", user="AuditCongress", password="?6n78$y\"\"~'Fvdy", database="auditcongress")

# Executes a single query string
def mysql_execute_query(mysql_conn, sql, use_database):
    mysql_cursor = mysql_conn.cursor()
    if use_database is not None:
        mysql_cursor.execute("USE "+use_database)

    mysql_cursor.execute(sql)

    result = [row[0] for row in mysql_cursor.fetchall()]

    mysql_cursor.close()
    return result

# Executes Many querys, based on executeMany. Best for inserts.
def mysql_execute_many_querys(mysql_conn, sql, data, database):
    mysql_cursor = mysql_conn.cursor()

    if database is not None:
        mysql_cursor.execute("USE "+database)

    mysql_cursor.executemany(sql, data)

    mysql_conn.commit()
    result = [row[0] for row in mysql_cursor.fetchall()]
    mysql_cursor.close()
    return result

#Built a thread given the target and arguments
def buildThread(target, args):
    return threading.Thread(target=target, args=(args,), daemon=True)
def getThreads(function, allOptions):
    threads = []
    for op in allOptions:
        thread = buildThread(function, op)
        threads.append(thread)
    return threads
def startThreads(threads): 
    for thread in threads:
        thread.start() 
def joinThreads(threads): 
    numAlive = len(threads)
    while numAlive > 0:
        numAlive = 0
        for thread in threads:
            thread.join(2)
            if thread.is_alive(): numAlive += 1
        time.sleep(1)    





def determineCongressNumberfromPath(url):
    lastSlash = url.rfind("/")
    lastDot = url.rfind(".")
    congress = url[lastSlash+1:lastDot] if lastDot > -1 else url[lastSlash+1:]
    
    return congress

def downloadBillZip(url):
    congress = determineCongressNumberfromPath(url)
    savePath = BILLS_DIR+congress
    downloadZipFile(url, savePath)
    print("Saved bulk bill data for congress", congress, "to", savePath)

def getBillZipUrls():
    soup = getParsedHtml(PROPUBLICA_BULK_BILLS_URL)
    links = soup.select(BILL_LINKS_SELECTOR)
    return [link["href"] for link in links]

def downloadBillZipfiles():
    urls = getBillZipUrls()
    print("started downloading", len(urls), "Zip files")

    threads = getThreads(downloadBillZip, urls)
    startThreads(threads)
    joinThreads(threads)



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
    actualBill["id"] = "{}{}-{}".format(typ, num, cong)

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
    actualBill["id"] = jsonData["bill_id"]

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
        id = cospon["bioguideId"] if "bioguideId" in cospon else getMemberByThomasId(cospon["thomas_id"])
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

    return billData

def insertBills(bills):
    mysql_conn = mysql_connect()

    billSql = "INSERT INTO Bills (id, type, congress, number, bioguideId, title, introduced, updated) "\
              "VALUES (%s, %s, %s, %s, %s, %s, %s, %s)"

    subjectSql = "INSERT INTO BillSubjects (type, congress, number, subject) VALUES (%s, %s, %s, %s)"
    titleSql = "INSERT INTO BillTitles (type, congress, number, title, titleType, titleAs, isForPortion) "\
               "VALUES (%s, %s, %s, %s, %s, %s, %s)"
    coSponSql = "INSERT INTO BillCoSponsors (type, congress, number, bioguideId, sponsoredAt, withdrawnAt, isOriginal) "\
                "VALUES (%s, %s, %s, %s, %s, %s, %s)"
    
    billData,subjectData,titleData,cosponData = [],[],[],[]

    for parsedBill in bills:
        bill = parsedBill["bill"]
        t,c,n = bill["type"],bill["congress"],bill["number"]
        bioguide = bill["bioguideId"]

        billData.append((bill["id"], t, c, n, bioguide, bill["title"], 
                         bill["introduced_at"], bill["updated_at"]))

        for subject in parsedBill["subjects"]: subjectData.append((t, c, n, subject))

        for title in parsedBill["titles"]: 
            isForPortion = title["is_for_portion"] if "is_for_portion" in title.keys() else ""
            titleData.append((t, c, n, title["title"], title["type"], title["as"], isForPortion))
        
        for cosponsor in parsedBill["cosponsors"]:
            cosponData.append((t, c, n, cosponsor["id"], cosponsor["sponsoredAt"], cosponsor["withdrawnAt"], cosponsor["isOriginal"]))


    mysql_execute_many_querys(mysql_conn, billSql, billData, "auditcongress")
    mysql_execute_many_querys(mysql_conn, subjectSql, subjectData, "auditcongress")
    mysql_execute_many_querys(mysql_conn, titleSql, titleData, "auditcongress")
    mysql_execute_many_querys(mysql_conn, coSponSql, cosponData, "auditcongress")
    mysql_conn.close()

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
                    #if len(bill["amendments"]) == 2:
                    #    pprint(bill["amendments"])
                    #    return 1
            else: skippedFiles += 1

    if skippedFiles > 0: print("Skipped",skippedFiles,"fdsys_billstatus.xml files")
    return bills

def readBillZip(filename):   
    congress = determineCongressNumberfromPath(filename)

    deleteThread = buildThread(deleteBills, congress)
    startThreads([deleteThread]) 

    startRead = datetime.now()
    bills = []
    totalRead = 0
    with ZipFile(filename, 'r') as zipped:
        bills = readZippedFiles(zipped)

    insertThread = buildThread(insertBills, bills)
    joinThreads([deleteThread])

    startThreads([insertThread]) 
    joinThreads([insertThread])

    zipped.close()
           
    print("Took",seconds_since(startRead),"seconds to parse then insert", len(bills), "bill files from", filename)
    time.sleep(2)

fullMultiThreading = False
threadPooling = False
poolSize = 1
noThreading = True    
def readBillZipFiles():
    zips = [BILLS_DIR+p for p in os.listdir(BILLS_DIR) if p.find(".zip") >= 0]
    print("Started parseing", len(zips), "Zip files")
    
    if fullMultiThreading:
        #26 Threads = ~215 Seconds (maxing SSD)
        threads = getThreads(readBillZip, zips)
        startThreads(threads)
        joinThreads(threads)
    elif threadPooling:
        #10 = ~180s (maxing SSD)
        #5 = ~195s   (maxing SSD)
        #4 = ~165s   (maxing SSD) == 4 is quickest
        #3 = ~185s   (maxing SSD)
        #2 = ~210s   (maxing SSD)
        with ThreadPoolExecutor(poolSize) as exe:
            for zipFile in zips:
                exe.submit(readBillZip, zipFile)
    elif noThreading:
        #sequential = ~260s
        for zipFile in zips:
            #if zipFile.find("109") >= 0 or zipFile.find("117") >= 0:
            #if zipFile.find("117") >= 0:
            #    readBillZip(zipFile)
            readBillZip(zipFile)

def deleteBills(congress=None): 
    mysql_conn = mysql_connect()
    startDelete = datetime.now()
    toDeleteFrom = ["Bills", "BillSubjects", "BillTitles", "BillCoSponsors"]
    sql = ""

    for table in toDeleteFrom:
        if congress is None: sql = "TRUNCATE {}".format(table)
        else: sql = "DELETE FROM {} WHERE congress = {}".format(table, congress)
        mysql_execute_query(mysql_conn, sql, "auditcongress")
    
    mysql_conn.commit()
    mysql_conn.close()

    print("Took", seconds_since(startDelete), "seconds to drop", toDeleteFrom, "for congress", congress)


def countBills(congress=None): 
    mysql_conn = mysql_connect()
    
    sql = ""
    if congress is None: sql = "SELECT COUNT(*) FROM BILLS"
    else: sql = "SELECT COUNT(*) FROM BILLS WHERE congress = {}".format(congress)

    count = mysql_execute_query(mysql_conn, sql, "auditcongress")[0]
    mysql_conn.close()
    return count


def getMemberByThomasId(thomasId):
    global MEMBERS_MAPPING
    try: return MEMBERS_MAPPING[thomasId]
    except Exception as e: return None

def setMemberMapping():
    global MEMBERS_MAPPING
    print("Fetching ThomasId to BioguideId mapping from",MEMBERS_MAPPING_API_URL)
    page = rq.get(MEMBERS_MAPPING_API_URL)
    MEMBERS_MAPPING = json.loads(page.content)["mapping"]

#900s to run
def doBulkBillPull():
    #First fetch the ThomasID => BioguideId mapping
    setMemberMapping()

    #Delete the cache before running
    if False and os.path.exists(BILLS_DIR):
        print("Deleting Existing Bills Cache...")
        shutil.rmtree(BILLS_DIR)
    
    startPull = datetime.now()

    if not os.path.exists(BILLS_DIR):
        downloadBillZipfiles()
        print("Took", seconds_since(startPull),"seconds to download",countFiles(BILLS_DIR),"zip files.")
    
    startExtract = datetime.now()
    readBillZipFiles()
    print("Took", seconds_since(startExtract),"seconds to parse & insert",countBills(),"bills files.")

if __name__ == "__main__":   
    doBulkBillPull()