import sys, os
sys.path.append(os.path.abspath("../"))

from shared import db
from datetime import datetime

def log(logLevel, logAction, logMessage):
    sql = "INSERT INTO LOG (level,language,action,message,time) VALUES (%s, %s, %s, %s, %s)"
    data = [[logLevel, "python", logAction, logMessage, datetime.now()]]
    db.runInsertingSql(sql, data)

def logInfo(logAction, logMessage): log("info", logAction, logMessage)

def logError(logAction, logMessage): log("error", logAction, logMessage)