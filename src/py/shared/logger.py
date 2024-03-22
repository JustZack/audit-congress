import sys, os
sys.path.append(os.path.abspath("../"))

from shared import db
from datetime import datetime

LOG_COLUMNS = ["level", "language", "action", "message", "time"]

def log(logLevel, logAction, logMessage):
    data = [logLevel, "python", logAction, logMessage, datetime.now()]
    db.insertRow("Log", LOG_COLUMNS, data)

def logInfo(logAction, logMessage): log("info", logAction, logMessage)

def logError(logAction, logMessage): log("error", logAction, logMessage)