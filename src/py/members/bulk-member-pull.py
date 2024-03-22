import os, time, shutil, io, json, math
from datetime import datetime

from pprint import pprint

import sys
sys.path.append(os.path.abspath("../"))

from shared import logger, db, zjthreads, util

def stopWithError(error):
    logger.logError(error)
    updateRunningStatus(False)


def scriptAlreadyRunning():
    sql = "SELECT isRunning FROM CacheStatus where source = 'bulk-member'"
    result = db.runReturningSql(sql)
    if (len(result) == 1): return bool(result[0])
    elif (len(result) == 0):
        sql = "INSERT INTO CacheStatus (source, status, isRunning) VALUES ('bulk-member', 'n/a', 1)"
        result = db.runCommitingSql(sql)
        return False
    return False

def updateRunningStatus(isRunning):
    sql = "UPDATE CacheStatus SET isRunning = {} WHERE source = 'bulk-member'".format(isRunning)
    db.runCommitingSql(sql)



def doSetup():
    logger.setLogAction("bulk-member")

    #Make sure the DB schema is valid first
    db.throwIfShemaInvalid()

#~2800s to run with 16MB cache (With Truncate)
#~1550s to run with 2048MB cache (With Truncate)
#~1500s to run with 4096MB cache (With Truncate)
def doBulkMemberPull():
    #Make sure the script isnt already running according to the DB
    if not scriptAlreadyRunning(): updateRunningStatus(True)
    else: raise Exception("Tried running script when it is already running! Exiting.")

def main():
    doSetup()
    doBulkMemberPull()

if __name__ == "__main__": util.runAndCatchMain(main, updateRunningStatus, False)
