import os, time, shutil, io, json, math
from datetime import datetime

from pprint import pprint

import sys
sys.path.append(os.path.abspath("../"))

from shared import logger, db, zjthreads, util

SCRIPT_NAME = "bulk-member"

BASE_URL = "https://theunitedstates.io/congress-legislators/"

CURRENT_LEGISLATORS_URL = "{}legislators-current.json".format(BASE_URL)
HISTORICAL_LEGISLATORS_URL = "{}legislators-historical.json".format(BASE_URL)
LEGISLATORS_SOCIALS_URL = "{}legislators-social-media.json".format(BASE_URL)
LEGISLATORS_OFFICES_URL = "{}legislators-district-offices.json".format(BASE_URL)
PRESIDENTS_URL = "{}executive.json".format(BASE_URL)
CURRENT_COMMITTEES_URL = "{}committees-current.json".format(BASE_URL)
CURRENT_COMMITTEE_LEGISLATORS_URL = "{}committee-membership-current.json".format(BASE_URL)
HISTORICAL_COMMITTEES_URL = "{}committees-historical.json".format(BASE_URL)


def doSetup():
    logger.setLogAction(SCRIPT_NAME)

    #Make sure the DB schema is valid first
    db.throwIfShemaInvalid()

def doBulkMemberPull():
    return

def main():
    doSetup()

    util.throwIfScriptAlreadyRunning(SCRIPT_NAME)

    util.updateScriptRunningStatus(SCRIPT_NAME, True)
    doBulkMemberPull()
    util.updateScriptRunningStatus(SCRIPT_NAME, False)

if __name__ == "__main__": util.runAndCatchMain(main, util.updateScriptRunningStatus, SCRIPT_NAME, False)
