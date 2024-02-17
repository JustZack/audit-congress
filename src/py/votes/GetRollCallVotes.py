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

def seconds_since(a): return (datetime.now()-a).total_seconds()

def saveFile(path, data):
    os.makedirs(os.path.dirname(path), exist_ok=True)
    file = open(path, "w")
    file.write(data)
    file.close()

def getParsedHtml(url):
    page = rq.get(url)
    print("GET HTML:", url)
    soup = BeautifulSoup(page.content, "html.parser")
    return soup

def getParsedXml(url):
    page = rq.get(url)
    print("GET XML:", url)
    soup = BeautifulSoup(page.content, "xml")
    return soup

def getLeadingZeroNumber(number, requiredLength):
    nStr = str(number)
    nDiff = requiredLength-len(nStr)
    return ("0"*nDiff)+nStr

def getCongressOptionsElements(url, selector):
    soup = getParsedHtml(url)
    dropdown = soup.select(selector)
    return dropdown

# Return all years for the given congress
def getYearsByCongress(congress):
    n2 = int(congress)*2
    # congress# * 2 + 1787 = first year of this congress session
    year1 = n2+1787
    #Then compute the second year
    #This differs because the 72nd congress sessions were 3 years, now they are 2
    year2 = n2+1788 if n2 > 72 else (n2+1789)

    return [year1, year2]


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
        year = parts[0]
        congress = parts[1][1:-3]
        session = parts[2][:-3]
        congresses.append({"congress": congress, "session": session, "year": year})
    
    return congresses
 

def getHouseVoteUrl(year,vote):
    voteStr = getLeadingZeroNumber(vote, 3)
    return HOUSE_VOTE_URL.format(year=year,vote=voteStr)

def getSenateVoteUrl(congress,session,vote):
    voteStr = getLeadingZeroNumber(vote, 5)
    return SENATE_VOTE_URL.format(congress=congress,session=session,vote=voteStr)


def isHouseServerErrorPage(html):
    header = html.select("#header h1")
    if (len(header) > 0):
        if header[0].text == "Server Error": 
            return True
    
    return False

def isSenateServerErrorPage(html):
    error = html.select("title")
    if (len(error) > 0):
        if error[0].text == "U.S. Senate: Roll Call Vote Unavailable": 
            return True
    return False


def iterateHouseVotes(config):
    validVoteFound = True
    congress = config["congress"]
    session = config["session"]
    year = config["year"]
    vote = 1

    while validVoteFound:
        url = getHouseVoteUrl(year, vote)
        voteHtml = getParsedXml(url)
        if isHouseServerErrorPage(voteHtml):
            print("End of votes in House for",congress,"session",session)
            validVoteFound = False
        else:
            fileName = VOTE_FILE_PATH.format("house", congress, session, vote)
            saveFile(fileName, str(voteHtml))
            vote += 1

def iterateSenateVotes(config):
    validVoteFound = True
    congress = config["congress"]
    session = config["session"]
    vote = 1

    while validVoteFound:
        url = getSenateVoteUrl(congress, session, vote)
        voteHtml = getParsedXml(url)
        if isSenateServerErrorPage(voteHtml):
            print("End of votes in Senate for",congress,"session",session)
            validVoteFound = False
        else:
            fileName = VOTE_FILE_PATH.format("senate", congress, session, vote)
            saveFile(fileName, str(voteHtml))
            vote += 1


def pullVotesWithThreadPool(function, allOptions):
    with ThreadPoolExecutor(max_workers=len(allOptions)) as executor:
        for op in allOptions:
            executor.submit(function, op)

def pullVotesWithThreads(function, allOptions):
    threads = []
    for op in allOptions:
        t = threading.Thread(target=function, args=(op,))
        threads.append(t)
        t.start()
    return threads

def waitForThreadJoin(threads):
    for thread in threads:
        thread.join()


def getVotes(configFunction, iterativeFunction):
    config = configFunction()
    return pullVotesWithThreads(iterativeFunction, config)

def getHouseVotes(): return getVotes(determineHouseConfig, iterateHouseVotes)

def getSenateVotes(): return getVotes(determineSenateConfig, iterateSenateVotes)


def doVotePull():
    if os.path.exists(VOTES_DIR): shutil.rmtree(VOTES_DIR)
    startPull = datetime.now()

    threads = []

    hThreads = getHouseVotes()
    print("Started Processing",len(hThreads),"House votes by session.")

    sThreads = getSenateVotes()
    print("Started Processing",len(sThreads),"Senate votes by session.")
    
    threads.extend(hThreads)
    threads.extend(sThreads)
    
    waitForThreadJoin(threads)

    print("Took", seconds_since(startPull))

if __name__ == "__main__":
    doVotePull()
    #iterateSenateVotes({'congress': 118, 'session': 2, 'year': 2023})
    #iterateHouseVotes({'congress': 118, 'session': 2, 'year': 2023})