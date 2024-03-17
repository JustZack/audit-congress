import db
from datetime import datetime

def log(logLevel, logAction, logMessage):
    sql = "INSERT INTO LOG (level,language,action,message,time) VALUES (%s, %s, %s, %s, %s)"
    data = [[logLevel, "python", logAction, logMessage, datetime.now()]]
    db.runInsertingSql(sql, data)

def logInfo(logAction, logMessage): log("info", logAction, logMessage)

def logError(type, message): log("error", logAction, logMessage)