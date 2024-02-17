import requests as rq
import os
import time
import shutil
from datetime import datetime
from bs4 import BeautifulSoup
import threading
from concurrent.futures import ThreadPoolExecutor


HOUSE_BASE_URL = "https://clerk.house.gov/"
HOUSE_CONFIG_URL = HOUSE_BASE_URL+"Votes"
HOUSE_CONFIG_SELECTOR = "form#members-votes-filter select#member-votes-congress option"
HOUSE_VOTE_URL = HOUSE_BASE_URL+"evs/{year}/roll{vote}.xml"

SENATE_BASE_URL = "https://www.senate.gov/legislative/"
SENATE_CONFIG_URL = SENATE_BASE_URL+"votes_new.htm"
SENATE_CONFIG_SELECTOR = "form[name='PastVotes'] select[name='menu'] option:not(:first-child)"
SENATE_VOTE_URL = SENATE_BASE_URL+"LIS/roll_call_votes/vote{congress}{session}/vote_{congress}_{session}_{vote}.xml"

VOTES_DIR = "cache"
VOTE_FILE_PATH = VOTES_DIR+"/{}/{}/{}/{}.xml"
VOTE_COLLECTION_FINISHED_MESSAGE = "End of votes ({}) in {} for congress {}, session {} ({})"

SECONDS_BETWEEN_THREAD_STARTS = .2
SECONDS_BETWEEN_VOTE_CALLS = 10

TEST_MODE = True
TEST_MODE_VOTES_PER_SESSION = 1
TEST_MODE_CONGRESSES_PER_CHAMBER = 1

def shouldStopVoteFetchForTestMode(voteNum):
    return TEST_MODE and TEST_MODE_VOTES_PER_SESSION > -1 and voteNum > TEST_MODE_VOTES_PER_SESSION
def shouldStopVoteStartForTestMode(voteStarts):
    return TEST_MODE and TEST_MODE_CONGRESSES_PER_CHAMBER > -1 and voteStarts >= TEST_MODE_CONGRESSES_PER_CHAMBER

SHOW_DEBUG_MESSAGES = False
def debug_print(*strs):
    if SHOW_DEBUG_MESSAGES: print(*strs)

def seconds_since(a): return (datetime.now()-a).total_seconds()
def countFiles(inDir):
    count = 0
    for root_dir, cur_dir, files in os.walk(inDir):
        count += len(files)
    return count

def saveFile(path, data):
    os.makedirs(os.path.dirname(path), exist_ok=True)
    file = open(path, "w")
    file.write(data)
    file.close()
def saveVoteFile(voteData, *fileNameArgs):
    fileName = VOTE_FILE_PATH.format(*fileNameArgs)
    saveFile(fileName, str(voteData))

def getParsedSoup(url, features="html.parser"):
    page = rq.get(url)
    debug_print("GET:", url)
    soup = BeautifulSoup(page.content, features)
    return soup
def getParsedHtml(url): return getParsedSoup(url)
def getParsedXml(url): return getParsedSoup(url, "xml")


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

def getCongressOptionsElements(url, selector):
    soup = getParsedHtml(url)
    dropdown = soup.select(selector)
    return dropdown
def determineHouseConfig():
    options = getCongressOptionsElements(HOUSE_CONFIG_URL,HOUSE_CONFIG_SELECTOR)

    congresses = []
    for option in options:
        congress = option["value"]
        years = getYearsByCongress(congress)
        session = 1
        for year in years:
            congresses.append({"congress": str(congress), "session": str(session), "year": str(year)})
            session+=1
    
    return congresses
def determineSenateConfig():
    options = getCongressOptionsElements(SENATE_CONFIG_URL,SENATE_CONFIG_SELECTOR)
    
    congresses = []
    for option in options:
        parts = option.text.split(" ")
        year = parts[0],
        congress = parts[1][1:-3]
        session = parts[2][:-3]
        congresses.append({"congress": congress, "session": session, "year": year})
    
    return congresses


def isPageTitle(html, titleStr):
    elem = html.select("title")
    if (len(elem) > 0 and elem[0].text == titleStr): 
        return True
    return False
def isHouseServerErrorPage(html): return isPageTitle(html, "404 - File or directory not found.")
def isSenateUnAuthorizedPage(html): return isPageTitle(html, "Senate.gov - Unauthorized")
def isSenateServerErrorPage(html): return isPageTitle(html, "U.S. Senate: Roll Call Vote Unavailable") or isSenateUnAuthorizedPage(html)


def getVoteUrl(voteCfg):
    chamber = voteCfg[0]
    if chamber == "house": return getHouseVoteUrl(voteCfg[4], voteCfg[3])
    elif chamber == "senate": return getSenateVoteUrl(voteCfg[1], voteCfg[2], voteCfg[3])
def getHouseVoteUrl(year,vote):
    voteStr = getLeadingZeroNumber(vote, 3)
    return HOUSE_VOTE_URL.format(year=year,vote=voteStr)
def getSenateVoteUrl(congress,session,vote):
    voteStr = getLeadingZeroNumber(vote, 5)
    return SENATE_VOTE_URL.format(congress=congress,session=session,vote=voteStr)

def handleVote(voteHtml, voteCfg, pageIsErrorCheckFunction):
    if pageIsErrorCheckFunction(voteHtml): 
        print(VOTE_COLLECTION_FINISHED_MESSAGE.format(voteCfg[3]-1, voteCfg[0], voteCfg[1], voteCfg[2], voteCfg[4]))
        return False
    else:
        saveVoteFile(voteHtml,*voteCfg)
        voteCfg[3] += 1
        return True
def iterateVotes(config, chamber, pageIsErrorCheckFunction):
    voteCfg = [chamber,config["congress"],config["session"],1,config["year"]]
    while True:
        url = getVoteUrl(voteCfg)
        voteHtml = getParsedXml(url)
        continuePullingVotes = handleVote(voteHtml, voteCfg, pageIsErrorCheckFunction)
        
        if shouldStopVoteFetchForTestMode(voteCfg[3]):  break
        elif continuePullingVotes:                      time.sleep(SECONDS_BETWEEN_VOTE_CALLS)
        else:                                           break
def iterateHouseVotes(config):  iterateVotes(config, "house", isHouseServerErrorPage)
def iterateSenateVotes(config): iterateVotes(config, "senate", isSenateServerErrorPage)


def pullVotesWithThreadPool(function, allOptions):
    with ThreadPoolExecutor(max_workers=len(allOptions)) as executor:
        [executor.submit(function, op) for op in allOptions]
            
def buildThread(target, args):
    return threading.Thread(target=target, args=(args,), daemon=True)
def getVoteThreads(function, allOptions):
    threads = []
    for op in allOptions:
        thread = buildThread(function, op)
        threads.append(thread)
        if shouldStopVoteStartForTestMode(len(threads)): break
    return threads

def startThreads(threads): 
    for thread in threads:
        thread.start() 
        time.sleep(SECONDS_BETWEEN_THREAD_STARTS)
def joinThreadsBlocking(threads): [thread.join() for thread in threads]
def joinThreadsNonBlocking(threads): 
    numAlive = len(threads)
    while numAlive > 0:
        numAlive = 0
        for thread in threads:
            thread.join(2)
            if thread.is_alive(): numAlive += 1
        time.sleep(1)    


def getVotes(configFunction, theadFunction): return getVoteThreads(theadFunction, configFunction())
def getHouseVotes(): return getVotes(determineHouseConfig, iterateHouseVotes)
def getSenateVotes(): return getVotes(determineSenateConfig, iterateSenateVotes)
def doVotePull():
    if os.path.exists(VOTES_DIR): shutil.rmtree(VOTES_DIR)
    startPull = datetime.now()

    hThreads = getHouseVotes()
    sThreads = getSenateVotes()
    threads = []
    threads.extend(hThreads)
    threads.extend(sThreads)
    
    print("Starting threads for",len(hThreads),"House and",len(sThreads),"senate votes by session.")
    startThreads(threads)
    joinThreadsNonBlocking(threads)
    print("Took", seconds_since(startPull),"seconds to pull",countFiles(VOTES_DIR),"votes.")

if __name__ == "__main__":
    doVotePull()
    #iterateSenateVotes({'congress': 118, 'session': 2, 'year': 2023})
    #iterateHouseVotes({'congress': 118, 'session': 2, 'year': 2023})