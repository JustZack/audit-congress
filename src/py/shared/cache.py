import os, math, json
from datetime import datetime

from shared import db

CACHE_TABLE_NAME = "CacheStatus"

def getCacheRow(scriptName):
    sql = "SELECT * FROM {} WHERE source = '{}'"\
        .format(CACHE_TABLE_NAME, scriptName)
    return db.runReturningSql(sql)

def cacheRowIsSet(scriptName):
    result = getCacheRow(scriptName)
    return len(result) > 0

def insertCacheRow(scriptName, status, isRunning):
    status = "" if status is None else status
    isRunning = 0 if isRunning is None else isRunning
    sql = "INSERT INTO {} (source, status, isRunning) VALUES ('{}', '{}', {})"\
        .format(CACHE_TABLE_NAME, scriptName, status, isRunning)
    db.runCommitingSql(sql)

def updateCacheRow(scriptName, status, isRunning):
    #Nothing to update if both are None
    if status is None and isRunning is None: return

    status = "status = '{}'".format(status) if status is not None else ""
    isRunning = "isRunning = {}".format(isRunning) if isRunning is not None else ""
    
    setVals = "{},{}" if len(status)>0 and len(isRunning)>0 else "{}{}"
    setVals = setVals.format(status, isRunning)

    sql = "UPDATE {} SET {} WHERE source = '{}'"\
        .format(CACHE_TABLE_NAME, setVals, scriptName)
    db.runCommitingSql(sql)

def setCacheRow(scriptName, status, isRunning):
    if cacheRowIsSet(scriptName): updateCacheRow(scriptName, status, isRunning)
    else: insertCacheRow(scriptName, status, isRunning)

def isScriptRunning(scriptName):
    isRunning = False
    if cacheRowIsSet(scriptName):
        sql = "SELECT isRunning FROM {} where source = '{}'"\
            .format(CACHE_TABLE_NAME, scriptName)
        result = db.runReturningSql(sql)[0]
        isRunning = bool(result[0])
    
    return isRunning

def setScriptRunning(scriptName, isRunning):
    setCacheRow(scriptName, None, isRunning)

def setScriptStatus(scriptName, status):
    setCacheRow(scriptName, status, None)

def throwIfScriptAlreadyRunning(scriptName):
    #Make sure the script isnt already running according to the DB
    if isScriptRunning(scriptName): raise Exception("Tried running script '{}' when it is already running! Exiting.".format(scriptName))


#if __name__ == "__main__":
    #throwIfScriptAlreadyRunning("test1")
    #print("here")
    #print(setScriptRunning("test5", False))
    #print(setScriptRunning("test1", False))
    #print(setScriptStatus("test4", datetime.now()))
    #print(setScriptRunning("test4", False))
    #print(getCacheRow("test1"))
    #print(cacheRowIsSet("test1"))
    #print(isScriptRunning("test1"))
    #print(setCacheRow("test1", "off", True))
    #print(isScriptRunning("test1"))

