import os, math, json
from datetime import datetime

import requests as rq
from bs4 import BeautifulSoup
import xmltodict as xml2d

from shared import logger, db

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

def saveFileAny(writeType, path, data):
    ensureFoldersExist(path)
    with open(path, writeType) as file: file.write(data)
    
def saveBinaryFile(path, data): saveFileAny("wb", path, data)
def saveFile(path, data): saveFileAny("w", path, data)

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


def isScriptRunning(scriptName):
    sql = "SELECT isRunning FROM CacheStatus where source = '{}'".format(scriptName)
    result = db.runReturningSql(sql)
    if (len(result) == 1): return bool(result[0])
    elif (len(result) == 0):
        sql = "INSERT INTO CacheStatus (source, status, isRunning) VALUES ('{}', 0, 0)".format(scriptName)
        result = db.runCommitingSql(sql)
        return False
    return False

def throwIfScriptAlreadyRunning(scriptName):
    #Make sure the script isnt already running according to the DB
    if isScriptRunning(scriptName): raise Exception("Tried running script '{}' when it is already running! Exiting.".format(scriptName))

def updateScriptRunningStatus(scriptName, isRunning):
    sql = "UPDATE CacheStatus SET isRunning = {} WHERE source = '{}'".format(isRunning, scriptName)
    db.runCommitingSql(sql)

def genericBulkScriptSetup(scriptName):
    #Set the log action
    logger.setLogAction(scriptName)

    #Make sure the DB schema is valid first
    db.throwIfShemaInvalid()

def genericBulkScriptMain(setupFunction, mainFunction, scriptName):
    setupFunction()

    throwIfScriptAlreadyRunning(scriptName)

    updateScriptRunningStatus(scriptName, True)
    mainFunction()
    updateScriptRunningStatus(scriptName, False)
