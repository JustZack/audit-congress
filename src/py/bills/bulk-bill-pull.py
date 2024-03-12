import os, time, shutil, io, json
from zipfile import ZipFile
from datetime import datetime

import xmltodict

import threading
from concurrent.futures import ThreadPoolExecutor

import requests as rq
from bs4 import BeautifulSoup
import mysql.connector

PROPUBLICA_BULK_BILLS_URL = "https://www.propublica.org/datastore/dataset/congressional-data-bulk-legislation-bills"
BILL_LINKS_SELECTOR = "table li a, div.info-panel div.actions a"

BILLS_DIR = "cache/"

MAX_THREAD_POOL_SIZE = 3
#50 => 630s (10min)
#20 => 620s (10min)
#10 => 415s (7min)
#5  => 345s (5min 45s) => 26 zip files, 5 threads per zip files = 130 concurrent threads max
#3  => 364s (6min)

#folders have varying structure
BILL_PATH_TYPE_ONE = 115 #If equals 115, use this
BILL_FOLDERS_ONE = "{}/{}/bills/" #Structure for 115 only

BILL_PATH_TYPE_TWO = 112 #Else if less than 112, use this
BILL_FOLDERS_TWO = "{}/bills/" #Structure for 93 -> 112

#Else, use this by default
BILL_FOLDERS_THR = "{}/congress/data/{}/bills/" #Structure for 113 -> 114, 116 onwards 

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



MYSQL_CON = None
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




def parseBillFDSYSXml(fileData):
    xmlData = xmltodict.parse(fileData)
    
    bill = xmlData["billStatus"]["bill"] 

    #print(bill["sponsors"])
    #print(bill["sponsors"])


    billData = dict()
    try:

        typ = bill["type"] if "type" in bill.keys() else bill["billType"]
        cong = bill["congress"]
        num = bill["number"] if "number" in bill.keys() else bill["billNumber"]

        actualBill = dict()
        actualBill["id"] = "{}{}-{}".format(typ, num, cong)

        actualBill["type"] = typ
        actualBill["congress"] = cong
        actualBill["number"] = num

        sponsor = bill["sponsors"]
        actualBill["sponsorThomasId"] = sponsor["item"]["bioguideId"] if sponsor is not None else ""

        actualBill["officialTitle"] = bill["title"]
        actualBill["popularTitle"] = bill["title"]
    except Exception as e:
        print(e,":", bill["sponsors"])

    #sponsor = xmlData.select("bill > sponsors bioguideId")
    #actualBill["sponsorThomasId"] = sponsor[0].text

    #title = xmlData.select("bill > title")[0].text
    #actualBill["officialTitle"] = title
    #actualBill["popularTitle"] = title

    billData["bill"] = actualBill
    #billData["titles"] = xmlData.select("bill > titles")[0]
    #billData["subjects"] = xmlData.select("bill > subjects")[0]
    #billData["cosponsors"] = xmlData.select("bill > cosponsors")[0]
    #billData["committees"] = xmlData.select("bill > committees")[0]
    #billData["amendments"] = xmlData.select("bill > amendments")[0]
    #billData["actions"] = xmlData.select("bill > actions")[0]

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
            actualBill["sponsorThomasId"] = sponsor["bioguide_id"]
        else:
            actualBill["sponsorThomasId"] = sponsor["thomas_id"]
    else:
        actualBill["sponsorThomasId"] = None

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
            actualBill["sponsorThomasId"] = sponsor["bioguide_id"]
        else:
            actualBill["sponsorThomasId"] = sponsor["thomas_id"]
    else:
        actualBill["sponsorThomasId"] = None

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

def insertBills(bills, mysql_conn):
    sql = "INSERT INTO Bills (id, type, congress, number, sponsorThomasId, officialTitle, popularTitle) VALUES (%s, %s, %s, %s, %s, %s, %s)"
       
    data = []
    for bill in bills:
        bill = bill["bill"]
        data.append((bill["id"], bill["type"], bill["congress"], bill["number"],
        bill["sponsorThomasId"], bill["officialTitle"], bill["popularTitle"]))

    mysql_execute_many_querys(mysql_conn, sql, data, "auditcongress")

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

def readZippedFiles(zipFile, mysql_conn):
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
            try:
                bill = None
                if file.find("data.json") >= 0: bill = parseBillDataJson(data)
                #elif file.find("data.xml") >= 0: bill = parseBillDataXml(data)
                elif file.find("fdsys_billstatus.xml") >= 0: bill = parseBillFDSYSXml(data)

                if bill is not None: 
                    totalFilesRead += 1
                    bills.append(bill)
                else: skippedFiles += 1
            except Exception as e:
                skippedFiles += 1
                print("Error from",file,":",str(e))
    
    insertBills(bills, mysql_conn)
    if skippedFiles > 0: print("Skipped",skippedFiles,"fdsys_billstatus.xml files")
    return totalFilesRead

def readBillZip(filename):
    startRead = datetime.now()
    mysql_conn = mysql_connect()
    
    totalRead = 0
    with ZipFile(filename, 'r') as zipped:
        totalRead = readZippedFiles(zipped, mysql_conn)
        
    mysql_conn.close()
    print("Took",seconds_since(startRead),"seconds to parse & insert", totalRead, "bill files from", filename)
    time.sleep(2)
        
def readBillZipFiles():
    zips = [BILLS_DIR+p for p in os.listdir(BILLS_DIR) if p.find(".zip") >= 0]
    print("Started parseing", len(zips), "Zip files")
    print("Dropping", countBills(), "bills...")
    truncateBills()

    #10 = ~180s (maxing SSD)
    #5 = ~190s   (maxing SSD)
    #4 = ~180s   (maxing SSD)
    #3 = ~200s   (maxing SSD)
    #2 = ~210s   (maxing SSD)
    with ThreadPoolExecutor(5) as exe:
        for zipFile in zips:
            exe.submit(readBillZip, zipFile)
    
    #26 Threads = ~196 Seconds (maxing SSD)
    #threads = getThreads(readBillZip, zips)
    #startThreads(threads)
    #joinThreads(threads)
    
    #sequential = 253s
    #for zipFile in zips:
        #if zipFile.find("113") >= 0:
    #    readBillZip(zipFile)

def truncateBills(): mysql_execute_query(mysql_connect(), "TRUNCATE BILLS", "auditcongress")
def countBills(): return mysql_execute_query(mysql_connect(), "select count(*) from BILLS", "auditcongress")[0]




def doBulkBillPull():
    #Delete the cache before running
    if False and os.path.exists(BILLS_DIR):
        print("Deleting Existing Bills Cache...")
        shutil.rmtree(BILLS_DIR)
    
    startPull = datetime.now()


    if not os.path.exists(BILLS_DIR):
        downloadBillZipfiles()
        print("Took", seconds_since(startPull),"seconds to download",countFiles(BILLS_DIR),"zip files.")
    
    MYSQL_CON = mysql_connect()
    
    startExtract = datetime.now()
    readBillZipFiles()
    print("Took", seconds_since(startExtract),"seconds to parse & insert",countBills(),"bills files.")

    MYSQL_CON.close()

if __name__ == "__main__":   
    doBulkBillPull()