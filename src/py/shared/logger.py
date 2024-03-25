import sys, os
sys.path.append(os.path.abspath("../"))

from shared import db
from datetime import datetime

LOG_COLUMNS = ["level", "language", "action", "message", "time"]
LOG_ACTION = ""

PRINT_LOG = True
def log(logLevel, *strs):
    logMessage = " ".join(str(item) for item in strs)
    if PRINT_LOG: print(logMessage)
    data = [logLevel, "python", LOG_ACTION, logMessage, datetime.now()]
    db.insertRow("Log", LOG_COLUMNS, data)

def logInfo(*strs): log("info", *strs)

def logError(*strs): log("error", *strs)

def setLogAction(action):
    global LOG_ACTION
    LOG_ACTION = action