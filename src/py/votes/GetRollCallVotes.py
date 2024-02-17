import requests as rq
import os
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

TEST_MODE = True
TEST_MODE_VOTES_PER_SESSION = 1
TEST_MODE_CONGRESSES_PER_CHAMBER = 1

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
def saveVoteFile(voteData, chamber, congress, session, vote):
    fileName = VOTE_FILE_PATH.format(chamber, congress, session, vote)
    saveFile(fileName, str(voteData))

def getParsedSoup(url, features="html.parser"):
    page = rq.get(url)
    #print("GET:", url)
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


def isErrorPage(html, errorString):
    elem = html.select("title")
    if (len(elem) > 0 and elem[0].text == "errorString"): 
        return True
    return False
def isHouseServerErrorPage(html): return isErrorPage(html, "404 - File or directory not found.")
def isSenateServerErrorPage(html): return isErrorPage(html, "U.S. Senate: Roll Call Vote Unavailable")
def isSenateUnAuthorizedPage(html): return isErrorPage(html, "Senate.gov - Unauthorized")


def getHouseVoteUrl(year,vote):
    voteStr = getLeadingZeroNumber(vote, 3)
    return HOUSE_VOTE_URL.format(year=year,vote=voteStr)
def getSenateVoteUrl(congress,session,vote):
    voteStr = getLeadingZeroNumber(vote, 5)
    return SENATE_VOTE_URL.format(congress=congress,session=session,vote=voteStr)


def iterateVotes(config, chamber, pageIsErrorCheckFunction):
    vote,congress,session,year = 1,config["congress"],config["session"],config["year"]
    while True:
        if chamber == "house": url = getHouseVoteUrl(year, vote)
        elif chamber == "senate": url = getSenateVoteUrl(congress, session, vote)

        voteHtml = getParsedXml(url)

        if pageIsErrorCheckFunction(voteHtml):
            print("End of votes in",chamber.title(),"for",congress,"session",session)
            break
        else:
            saveVoteFile(voteHtml,chamber,congress,session,vote)
            vote += 1 

        if TEST_MODE and vote >= TEST_MODE_VOTES_PER_SESSION: break
def iterateHouseVotes(config):  iterateVotes(config, "house", isHouseServerErrorPage)
def iterateSenateVotes(config): iterateVotes(config, "senate", isSenateServerErrorPage)


def pullVotesWithThreadPool(function, allOptions):
    with ThreadPoolExecutor(max_workers=len(allOptions)) as executor:
        [executor.submit(function, op) for op in allOptions]
            
def buildThread(target, args):
    return threading.Thread(target=target, args=(args,), daemon=True)
def pullVotesWithThreads(function, allOptions):
    threads = []
    for op in allOptions:
        thread = buildThread(function, op)
        thread.start()
        threads.append(thread)

        if TEST_MODE and len(threads) >= TEST_MODE_CONGRESSES_PER_CHAMBER: break
    return threads

def waitForThreadsJoin(threads): [thread.join() for thread in threads]


def getVotes(configFunction, theadFunction): return pullVotesWithThreads(theadFunction, configFunction())
def getHouseVotes(): return getVotes(determineHouseConfig, iterateHouseVotes)
def getSenateVotes(): return getVotes(determineSenateConfig, iterateSenateVotes)
def doVotePull():
    if os.path.exists(VOTES_DIR): shutil.rmtree(VOTES_DIR)
    startPull = datetime.now()

    threads = []

    hThreads = getHouseVotes()
    threads.extend(hThreads)
    print("Started Processing",len(hThreads),"House votes by session.")

    #sThreads = getSenateVotes()
    #threads.extend(sThreads)
    #print("Started Processing",len(sThreads),"Senate votes by session.")
    
    
    waitForThreadsJoin(threads)

    print("Took", seconds_since(startPull),"seconds to pull",countFiles(VOTES_DIR),"votes.")

if __name__ == "__main__":
    doVotePull()
    #iterateSenateVotes({'congress': 118, 'session': 2, 'year': 2023})
    #iterateHouseVotes({'congress': 118, 'session': 2, 'year': 2023})