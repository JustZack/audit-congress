import requests as rq
from bs4 import BeautifulSoup

HOUSE_BASE_URL = "https://clerk.house.gov/"
HOUSE_CONFIG_URL = HOUSE_BASE_URL+"Votes"
HOUSE_CONFIG_SELECTOR = "form#members-votes-filter select#member-votes-congress option"
HOUSE_VOTE_URL = HOUSE_BASE_URL+"evs/{year}/roll{vote}.xml"


SENATE_BASE_URL = "https://www.senate.gov/legislative/"
SENATE_CONFIG_URL = SENATE_BASE_URL+"votes_new.htm"
SENATE_CONFIG_SELECTOR = "form[name='PastVotes'] select[name='menu'] option:not(:first-child)"
SENATE_VOTE_URL = SENATE_BASE_URL+"LIS/roll_call_votes/vote{congress}{session}/vote_{congress}_{session}_{vote}.htm"

def getParsedHtml(url):
    page = rq.get(url)
    soup = BeautifulSoup(page.content, "html.parser")
    return soup

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
    url = HOUSE_VOTE_URL.format(year=year, vote=voteStr)
    return url

def getSenateVoteUrl(congress,session,vote):
    voteStr = getLeadingZeroNumber(vote, 5)
    url = SENATE_VOTE_URL.format(congress=congress,session=session, vote=voteStr)
    return url

def getLeadingZeroNumber(number, requiredLength):
    nStr = str(number)
    nDiff = requiredLength-len(nStr)
    return ("0"*nDiff)+nStr


def getHouseVotes():
    config = determineHouseConfig()
    
    startingUrls = []
    for op in config: houseVoteURLS.append(getHouseVoteUrl(op["year"], 1))

    return houseVoteURLS

def getSenateVotes():
    config = determineSenateConfig()

    startingUrls = []
    for op in config: senateVoteURLS.append(getSenateVoteUrl(op["congress"], op["session"], 1))

    return senateVoteURLS

if __name__ == "__main__":
    hVotes = getHouseVotes()
    sVotes = getSenateVotes()