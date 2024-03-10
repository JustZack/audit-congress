import os, time, shutil, io, json
from zipfile import ZipFile
from datetime import datetime

import threading
from concurrent.futures import ThreadPoolExecutor

import requests as rq
from bs4 import BeautifulSoup
import mysql.connector

PROPUBLICA_BULK_BILLS_URL = "https://www.propublica.org/datastore/dataset/congressional-data-bulk-legislation-bills"
BILL_LINKS_SELECTOR = "table li a, div.info-panel div.actions a"

BILLS_DIR = "cache"
BILL_ZIPS_DIR = BILLS_DIR+"/zips/"
BILL_DATA_DIR = BILLS_DIR+"/data/"

#folders have varying structure
BILL_PATH_TYPE_ONE = 115 #If equals 115, use this
BILL_FOLDERS_ONE = "{}/{}/bills/" #Structure for 115 only

BILL_PATH_TYPE_TWO = 112 #Else if less than 112, use this
BILL_FOLDERS_TWO = "{}/bills/" #Structure for 93 -> 112

#Else, use this by default
BILL_FOLDERS_THR = "{}/congress/data/{}/bills/" #Structure for 113 -> 114, 116 onwards 

BILL_TYPE_FOLDERS = ["hr", "hconres", "hjres", "hres", 
                     "s" , "sconres", "sjres", "sres"]

MAX_THREAD_POOL_SIZE = 5
#50 => 630s (10min)
#20 => 620s (10min)
#10 => 415s (7min)
#5  => 345s (5min 45s) => 26 zip files, 5 threads per zip files = 130 concurrent threads max
#3  => 364s (6min)

SHOW_DEBUG_MESSAGES = False
def debug_print(*strs):
    if SHOW_DEBUG_MESSAGES: print(*strs)

def seconds_since(a): return (datetime.now()-a).total_seconds()
def countFiles(inDir):
    count = 0
    for root_dir, cur_dir, files in os.walk(inDir):
        count += len(files)
    return count

def ensureFoldersExist(path):
    os.makedirs(os.path.dirname(path), exist_ok=True)

def saveFileAny(writeType, path, data):
    ensureFoldersExist(path)
    file = open(path, writeType)
    file.write(data)
    file.close()

def saveBinaryFile(path, data):
    saveFileAny("wb", path, data)

def saveFile(path, data):
    saveFileAny("w", path, data)

def downloadZipFile(url, savePath):
    ensureFoldersExist(savePath)
    file = rq.get(url)
    saveBinaryFile(savePath+".zip", file.content)

def getAndExtractZipFile(url, savePath):
    ensureFoldersExist(savePath)

    file = rq.get(url, stream=True)
    zipped = ZipFile(io.BytesIO(file.content))
    zipped.extractall(savePath)

def getParsedSoup(url, features="html.parser"):
    page = rq.get(url)
    debug_print("GET:", url)
    soup = BeautifulSoup(page.content, features)
    return soup
def getParsedHtml(url): return getParsedSoup(url)
def getParsedXml(url): return getParsedSoup(url, "xml")






# Opens a connection with a MySQL host
def mysql_connect(host, user, password):
    return mysql.connector.connect(host=host, user=user, password=password)

# Executes a single query string
def mysql_execute_query(mysql_con, sql, use_database):
    mysql_cursor = mysql_con.cursor()
    if use_database is not None:
        mysql_cursor.execute("USE "+use_database)

    mysql_cursor.execute(sql)
    if not returnDict:
        result = [row[0] for row in mysql_cursor.fetchall()]
    else:
        result = dict(mysql_cursor.fetchall())

    mysql_cursor.close()
    return result

# Executes Many querys, based on executeMany. Best for inserts.
def mysql_execute_many_querys(mysql_con, sql, data, database):
    mysql_cursor = mysql_con.cursor()

    mysql_cursor.execute("USE "+database)

    mysql_cursor.executemany(sql, data)

    mysql_con.commit()
    result = [row[0] for row in mysql_cursor.fetchall()]
    mysql_cursor.close()
    return result



def getLeadingZeroNumber(number, minLength):
    nStr = str(number)
    nDiff = minLength-len(nStr)
    return nStr if nDiff <= 0 else ("0"*nDiff)+nStr
def getYearsByCongress(congress):
    n2 = int(congress)*2
    # congress# * 2 + 1787 = first year of this congress session
    year1 = n2+1787
    #Then compute the second year
    #This differs because the 72nd congress sessions were 3 years, now they are 2
    year2 = n2+1788 if n2 > 72 else (n2+1789)

    return [year1, year2]



def determineCongressNumberfromPath(url):
    lastSlash = url.rfind("/")
    lastDot = url.rfind(".")
    congress = url[lastSlash+1:lastDot] if lastDot > -1 else url[lastSlash+1:]
    
    return congress

def getBillZipUrls():
    soup = getParsedHtml(PROPUBLICA_BULK_BILLS_URL)
    links = soup.select(BILL_LINKS_SELECTOR)
    return [link["href"] for link in links]

def startThreadsWithPool(function, allOptions):
    with ThreadPoolExecutor(max_workers=MAX_THREAD_POOL_SIZE) as executor:
        [executor.submit(function, op) for op in allOptions]
            
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


def mysql_connect(host, username, password,database):
    return mysql.connector.connect(host=host,user=username,password=password,database=database)





def downloadBillZip(url):
    congress = determineCongressNumberfromPath(url)
    savePath = BILL_ZIPS_DIR+congress
    downloadZipFile(url, savePath)
    print("Saved bulk bill data for congress", congress, "to", savePath)

def downloadBillZipfiles():
    urls = getBillZipUrls()
    print("started downloading", len(urls), "Zip files")

    threads = getThreads(downloadBillZip, urls)
    startThreads(threads)
    joinThreads(threads)






def extractZipFiles(zipFile, files, saveDir):
    with ThreadPoolExecutor(MAX_THREAD_POOL_SIZE) as exe:
        for file in files:
            data = zipFile.read(file)
            path = saveDir+"/"+file
            data = data.decode(json.detect_encoding(data))
            exe.submit(saveFile, path, data)

def extractBillZip(filename):
    startExtract = datetime.now()

    zipPath = filename
    dataPath = BILL_DATA_DIR+determineCongressNumberfromPath(filename)
    zipped = ZipFile(zipPath, 'r')
    files = zipped.namelist()
    extractZipFiles(zipped, files, dataPath)
    zipped.close()

    print(format(seconds_since(startExtract)),"Extracted", len(files), "files from", zipPath, "to", dataPath)
        
def extractBillZipFiles():
    zips = [BILL_ZIPS_DIR+p for p in os.listdir(BILL_ZIPS_DIR) if p.find(".zip") >= 0]
    print("Started extracting", len(zips), "Zip files")

    extractBillZip(zips[0])
    #threads = getThreads(extractBillZip, zips)
    #startThreads(threads)
    #joinThreads(threads)







def parseBillData(filePath):
    billData = dict()
    with open(filePath, "r") as file:  
        jsonData = json.load(file)

        actualBill = dict()
        actualBill["id"] = jsonData["bill_id"]

        actualBill["type"] = jsonData["bill_type"]
        actualBill["congress"] = jsonData["congress"]
        actualBill["number"] = jsonData["number"]

        actualBill["sponsorThomasId"] = jsonData["sponsor"]["thomas_id"]

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

def insertBills(mysql_con, bills):
    sql = "INSERT INTO bills (id, type, congress, number, sponsorThomasId, officialTitle, popularTitle) VALUES (%s, %s, %s, %s, %s, %s, %s)"
       
    data = []
    for bill in bills:
        bill = bill["bill"]
        data.append((bill["id"], bill["type"], bill["congress"], bill["number"],
        bill["sponsorThomasId"], bill["officialTitle"], bill["popularTitle"]))

    mysql_execute_many_querys(mysql_con, sql, data, "auditcongress")

def parseBillType(typePath):
    mysql_con = mysql_connect("127.0.0.1", "AuditCongress", "?6n78$y\"\"~'Fvdy", "auditcongress")
    folders = os.listdir(typePath)
    bills = []
    for folder in folders:
        data = "{}/{}/{}".format(typePath,folder,"data.json")
        bill = parseBillData(data)
        bills.append(bill)
    insertBills(mysql_con, bills)
    mysql_con.close()
            
def parseBillFolder(folderPath):
    congress = int(determineCongressNumberfromPath(folderPath))
    if congress == BILL_PATH_TYPE_ONE:
        typePath = BILL_FOLDERS_ONE.format(folderPath, congress) 
    elif congress <= BILL_PATH_TYPE_TWO:
        typePath = BILL_FOLDERS_TWO.format(folderPath) 
    else: 
        typePath = BILL_FOLDERS_THR.format(folderPath, congress) 

    folders = os.listdir(typePath)
    folders = [typePath+folder for folder in folders if folder in BILL_TYPE_FOLDERS]
    print("Started parsing bills in", congress, "congress")

    parseBillType(folders[0])
    #threads = getThreads(parseBillType, folders)
    #startThreads(threads)
    #joinThreads(threads)

def parseBillFiles():
    congresses = os.listdir(BILL_DATA_DIR)
    congresses = [BILL_DATA_DIR+congress for congress in congresses]

    parseBillFolder(congresses[0])
    #threads = getThreads(parseBillFolder, congresses)
    #startThreads(threads)
    #joinThreads(threads)





def doBulkBillPull():
    if os.path.exists(BILLS_DIR):
        print("Deleting Existing Bills Cache...")
        #if os.path.exists(BILL_ZIPS_DIR): shutil.rmtree(BILL_ZIPS_DIR)
        #if os.path.exists(BILL_DATA_DIR): shutil.rmtree(BILL_DATA_DIR)
        #shutil.rmtree(BILLS_DIR)
    
    startPull = datetime.now()

    if not os.path.exists(BILL_ZIPS_DIR):
        downloadBillZipfiles()
        print("Took", seconds_since(startPull),"seconds to download",countFiles(BILL_ZIPS_DIR),"zip files.")
    
    startExtract = datetime.now()
    if not os.path.exists(BILL_DATA_DIR):
        extractBillZipFiles()
        print("Took", seconds_since(startExtract),"seconds to extract",countFiles(BILL_DATA_DIR),"zipped files.")

    startParse = datetime.now()
    parseBillFiles()
    #print("Took", seconds_since(startParse),"seconds to parse",countFiles(BILL_DATA_DIR),"bill files.")

if __name__ == "__main__":   
    doBulkBillPull()