import time
from shared import util

CURRENT_CONGRESS_API_URL = "http://localhost/audit-congress/api.php?route=congress&current=1"
CURRENT_CONGRESS = None

def fetchCurrentCongress():
    global CURRENT_CONGRESS
    resp = util.getParsedJson(CURRENT_CONGRESS_API_URL)
    if "congress" in resp:
        CURRENT_CONGRESS =  str(resp["congress"]["number"])
        return True
    else:
        return False



def getMemberRow(member, isCurrent):
    mRow = []
    mId, mName, mBio = member["id"], member["name"], member["bio"]

    mIdFields = ["bioguide", "thomas", "lis", "govtrack", "opensecrets", "votesmart", "cspan", 
                "maplight", "icpsr", "wikidata", "google_entity_id"]
    mNameFields = ["official_full", "first", "last"]
    mBioFields = ["gender", "birthday"]
    
    mRow.extend(util.getFields(mId, mIdFields))
    mRow.extend(util.getFields(mName, mNameFields))
    mRow.extend(util.getFields(mBio, mBioFields))
    mRow.append(isCurrent)
    return mRow

def getTermRows(terms, bioguideId):
    mTerms = []
    termFields = ["bioguideId", "type", "start", "end", "state", "district", "party", "class", "how",
                   "state_rank", "url", "rss_url", "contact_form", "address", "office", "phone"]
    for term in terms:
        term["bioguideId"] = bioguideId
        mTerms.append(util.getFields(term, termFields))
    return mTerms

def getElectionRows(elections, bioguideId):
    mElections = []
    for election in elections:
        mElection = []
        mElection.append(election)
        mElection.append(bioguideId)

        mElections.append(mElection)
    return mElections

def getSocialRow(social):
    sRow = []
    sId, sSocial = social["id"], social["social"]

    sSocialFields = ["twitter", "twitter_id", "facebook", "facebook_id", 
                     "youtube", "youtube_id", "instagram", "instagram_id"]

    sRow.append(sId["bioguide"])
    sRow.extend(util.getFields(sSocial, sSocialFields))
    return sRow

def getOfficeRows(bioguideId, offices):
    mOffices = []
    officeFields = ["id", "bioguideId", "address", "suite", "building", 
                    "city", "state", "zip", "latitude", "longitude", "phone", "fax"]
    for office in offices:
        office["bioguideId"] = bioguideId
        mOffices.append(util.getFields(office, officeFields))
    return mOffices

def getSubCommitteeHistoryRows(subcommittees):
    subComHistData = []
    for sub in subcommittees: 
        subComHistData.extend(getCommitteeHistoryRows(subcommittees[sub]))
    return subComHistData

def getCommitteeHistoryRows(committee):
    commHistData = []
    committeeHistFields = ["thomas_id", "parent_id", "type", "congress", "name"]
    for congress in committee["congresses"]:
        committee["congress"] = congress
        committee["name"] = committee["names"][str(congress)]
        commHistData.append(util.getFields(committee, committeeHistFields))
    return commHistData

def getSubCommitteeRows(subcommittees, parentId, parentType):
    subComData = []
    for sub in subcommittees: 
        subCom = subcommittees[sub]
        subCom["type"] = parentType
        subCom["parent_id"] = parentId
        subCom["thomas_id"] = parentId+subCom["thomas_id"]
        subComData.append(getCommitteeRow(subCom))
    return subComData

def getCommitteeRow(committee):
    committeeFields = ["thomas_id", "parent_id", "type", "name", "wikipedia", "jurisdiction", 
                       "jurisdiction_source", "url", "rss_url", "minority_url", "minority_rss_url", 
                       "youtube_id", "address", "phone", "isCurrent"]
    return util.getFields(committee, committeeFields)



def getCombinedCommittee(code, current, historic):
    currentSub, historicSub = [], []
    data, isCurrent = None, False

    if code in current:
        isCurrent = True
        currentSub = util.getIfSet(current[code], "subcommittees", [])
        data = current[code]
        data["names"] = {CURRENT_CONGRESS: data["name"]}
        data["congresses"] = [CURRENT_CONGRESS]
        
    if code in historic:
        historicSub = util.getIfSet(historic[code], "subcommittees", [])
        if not isCurrent:
            data = historic[code]
        else: 
            data["names"].update(historic[code]["names"])
            data["congresses"].extend(historic[code]["congresses"])

    if len(currentSub) > 0 or len(historicSub) > 0:
        subCurrent = util.dictArrayToDict(currentSub, "thomas_id")
        subHistoric = util.dictArrayToDict(historicSub, "thomas_id")
        data["subcommittees"] = getAggregatedCommittees(subCurrent, subHistoric)
    
    data["isCurrent"] = isCurrent
    return data

def getAggregatedCommittees(current, historic):
    aggregated = dict()
    cSet = set(current.keys())
    cSet.update(historic.keys())

    for code in cSet: aggregated[code] = getCombinedCommittee(code, current, historic)
    return aggregated



def getMembersipRows(members, committee):
    membershipData = []
    membershipFields = ["bioguide", "party", "title", "rank"]
    for member in members:
        cMember = []
        cMember.append(committee)
        cMember.extend(util.getFields(member, membershipFields))
        membershipData.append(cMember)
    return membershipData