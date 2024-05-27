import os, math, json, csv
from datetime import datetime

import requests as rq
from bs4 import BeautifulSoup
import xmltodict as xml2d

from shared import logger, db, cache

def getFieldIfExists(theDict, theField): return theDict[theField] if theField in theDict else ""

def dictArrayToDict(dictArray, dictKeyToUse):
    newDict = dict()
    for aDict in dictArray: newDict[aDict[dictKeyToUse]] = aDict
    return newDict

def seconds_since(a): return (datetime.now()-a).total_seconds()

def countFiles(inDir):
    count = 0
    for root_dir, cur_dir, files in os.walk(inDir): count += len(files)
    return count

def ensureFoldersExist(path): os.makedirs(os.path.dirname(path), exist_ok=True)

def readFile(path):
    with open(path, "r") as file: return file.read()
def readJsonFile(path): return json.loads(readFile(path))

def saveFileAny(writeType, path, data):
    ensureFoldersExist(path)
    with open(path, writeType) as file: file.write(data)
def saveBinaryFile(path, data): saveFileAny("wb", path, data)
def saveFile(path, data): saveFileAny("w", path, data)
def saveAsCSV(path, data, headers=None):
    if len(data) == 0: return
    ensureFoldersExist(path)
    with open(path, "w", newline='\n', encoding='utf-8') as file:
        writer = None
        if type(data) is dict: writer = writeCSVAsDict(file, data, headers)
        elif type(data) is list: writer = writeCSVAsList(file, data, headers)

        if writer == None: raise Exception("saveAsCSV: Expected data of type dict or list, found: {}".format(type(data)))
        writeCSVRows(writer, data)

def writeCSVAsDict(file, data, headers):
    #Use headers if defined, else use the dict keys as header
    fields = headers if headers is not None else data[0].keys()
    writer = csv.DictWriter(file, fields)
    writer.writeheader()
    return writer

def writeCSVAsList(file, data, headers):
    writer = csv.writer(file, delimiter=",",)
    #Only write headers if they were supplied
    if headers is not None and type(headers) is list: 
        writer.writerow(headers)
    return writer

def writeCSVRows(writer, data):
    #Write rows based on how the writer was initialized
    for row in data: writer.writerow(row)

def readCSV(path): 
    contents = []
    with open(path, newline='') as file:
        reader = csv.DictReader(file)
        for row in reader: contents.append(row)
    return contents





def deleteFiles(fileList): [os.remove(f) for f in fileList]

def get(url): return rq.get(url).content

def getParsedSoup(url, features="html.parser"):
    soup = BeautifulSoup(get(url), features)
    return soup
def getHtmlSoup(url): return getParsedSoup(url)
def getXmlSoup(url): return getParsedSoup(url, "xml")

def getParsedJson(url): return getParsedJsonFile(get(url))
def getParsedJsonFile(fileData): return json.loads(fileData)

def getParsedXml(url): return getParsedXmlFile(get(url))
def getParsedXmlFile(fileData): return xml2d.parse(fileData)

def downloadZipFile(url, savePath): saveBinaryFile(savePath, get(url))

def chunkList(array, chunkSize):
    chunckedList = []
    numChunks = math.ceil(len(array)/chunkSize)
    for n in range(numChunks):
        start = n*chunkSize
        end = (n+1)*chunkSize
        chunckedList.append(array[start:end])
    return chunckedList

def csvStr(itemArray): return ",".join(itemArray)

def getPathDirectory(path):
    lastSlash = path.rfind("/") + 1
    return path[0:lastSlash]

def getPathFile(path):
    lastSlash = path.rfind("/") + 1
    return path[lastSlash:]

def pathIsFile(path):
    lastSlash = path.rfind("/") + 1
    lastDot = path.rfind(".") + 1
    return lastDot > lastSlash

def relativeToAbsPath(path): return os.path.abspath(path)

def logExceptionThen(exceptionMessage, onExceptFunction=None, *onExceptArguments):
        logger.logError(exceptionMessage)
        if onExceptFunction is not None: onExceptFunction(*onExceptArguments)

def runAndCatchMain(mainFunction, onExceptFunction=None, *onExceptArguments):
    try:          
        mainFunction()
    except KeyboardInterrupt: 
        logExceptionThen("Manually ended script via ctrl+c", onExceptFunction, *onExceptArguments)
    except Exception as e:    
        logExceptionThen("Stopped with Exception: {}".format(e), onExceptFunction, *onExceptArguments)

def genericBulkScriptSetup(scriptName):
    #Set the log action
    logger.setLogAction(scriptName)

    #Make sure the DB schema is valid first
    db.throwIfShemaInvalid()

def genericBulkScriptMain(setupFunction, mainFunction, scriptName=None):
    setupFunction()

    if scriptName is not None: cache.throwIfScriptAlreadyRunning(scriptName)

    mainFunction()
