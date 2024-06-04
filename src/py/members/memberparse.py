import time
from shared import util

CURRENT_CONGRESS_API_URL = "http://localhost/audit-congress/api.php?route=congress&current=1"
CURRENT_CONGRESS = None

def fetchCurrentCongress():
    global CURRENT_CONGRESS
    resp = util.getParsedJson(CURRENT_CONGRESS_API_URL)
    if "congress" in resp:
        CURRENT_CONGRESS =  resp["congress"]["number"]
        return True
    else:
        return False



def getMemberRow(member, isCurrent):
    mRow = []
    mId, mName, mBio = member["id"], member["name"], member["bio"]
    mRow.append(mId["bioguide"])
    mRow.append(util.getIfSetAsStr(mId, "thomas"))
    mRow.append(util.getIfSetAsStr(mId, "lis"))
    mRow.append(util.getIfSetAsStr(mId, "govtrack"))
    mRow.append(util.getIfSetAsStr(mId, "opensecrets"))
    mRow.append(util.getIfSetAsStr(mId, "votesmart"))
    mRow.append(util.getIfSetAsStr(mId, "cspan"))
    mRow.append(util.getIfSetAsStr(mId, "maplight"))
    mRow.append(util.getIfSetAsStr(mId, "icpsr"))
    mRow.append(util.getIfSetAsStr(mId, "wikidata"))
    mRow.append(util.getIfSetAsStr(mId, "google_entity_id"))

    mRow.append(util.getIfSetAsStr(mName, "official_full"))
    mRow.append(util.getIfSetAsStr(mName, "first"))
    mRow.append(util.getIfSetAsStr(mName, "last"))

    mRow.append(util.getIfSetAsStr(mBio, "gender"))
    mRow.append(util.getIfSetAsStr(mBio, "birthday"))

    mRow.append(isCurrent)
    
    return mRow

def getTermRows(terms, bioguideId):
    mTerms = []
    for term in terms:
        mTerm = []
        mTerm.append(bioguideId)
        mTerm.append(util.getIfSetAsStr(term, "type"))
        mTerm.append(util.getIfSetAsStr(term, "start"))
        mTerm.append(util.getIfSetAsStr(term, "end"))
        mTerm.append(util.getIfSetAsStr(term, "state"))
        mTerm.append(util.getIfSetAsStr(term, "district"))
        mTerm.append(util.getIfSetAsStr(term, "party"))
        mTerm.append(util.getIfSetAsStr(term, "class"))
        mTerm.append(util.getIfSetAsStr(term, "how"))
        mTerm.append(util.getIfSetAsStr(term, "state_rank"))
        mTerm.append(util.getIfSetAsStr(term, "url"))
        mTerm.append(util.getIfSetAsStr(term, "rss_url"))
        mTerm.append(util.getIfSetAsStr(term, "contact_form"))
        mTerm.append(util.getIfSetAsStr(term, "address"))
        mTerm.append(util.getIfSetAsStr(term, "office"))
        mTerm.append(util.getIfSetAsStr(term, "phone"))

        mTerms.append(mTerm)
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

    sRow.append(sId["bioguide"])
    sRow.append(util.getIfSetAsStr(sSocial, "twitter"))
    sRow.append(util.getIfSetAsStr(sSocial, "twitter_id"))
    sRow.append(util.getIfSetAsStr(sSocial, "facebook"))
    sRow.append(util.getIfSetAsStr(sSocial, "facebook_id"))
    sRow.append(util.getIfSetAsStr(sSocial, "youtube"))
    sRow.append(util.getIfSetAsStr(sSocial, "youtube_id"))
    sRow.append(util.getIfSetAsStr(sSocial, "instagram"))
    sRow.append(util.getIfSetAsStr(sSocial, "instagram_id"))

    return sRow



def getOfficeRows(bioguideId, offices):
    mOffices = []
    for office in offices:
        mOffice = []

        mOffice.append(util.getIfSetAsStr(office, "id"))
        mOffice.append(bioguideId)
        mOffice.append(util.getIfSetAsStr(office, "address"))
        mOffice.append(util.getIfSetAsStr(office, "suite"))
        mOffice.append(util.getIfSetAsStr(office, "building"))
        mOffice.append(util.getIfSetAsStr(office, "city"))
        mOffice.append(util.getIfSetAsStr(office, "state"))
        mOffice.append(util.getIfSetAsStr(office, "zip"))
        mOffice.append(util.getIfSetAsStr(office, "latitude"))
        mOffice.append(util.getIfSetAsStr(office, "longitude"))
        mOffice.append(util.getIfSetAsStr(office, "phone"))
        mOffice.append(util.getIfSetAsStr(office, "fax"))

        mOffices.append(mOffice)
    return mOffices



def getSubCommitteeHistoryRows(subcommittees):
    subComHistData = []
    for sub in subcommittees: 
        subComHistData.extend(getCommitteeHistoryRows(subcommittees[sub]))
    return subComHistData

def getCommitteeHistoryRows(committee):
    commHistData = []
    for congress in committee["congresses"]:
        histData = []
        name = committee["names"][str(congress)]
        histData.append(committee["thomas_id"])
        histData.append(util.getIfSetAsStr(committee, "parent_id"))
        histData.append(util.getIfSetAsStr(committee, "type"))
        histData.append(congress)
        histData.append(name)
        commHistData.append(histData)
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
    cRow = []

    cRow.append(committee["thomas_id"])
    cRow.append(util.getIfSetAsStr(committee, "parent_id"))
    cRow.append(util.getIfSetAsStr(committee, "type"))
    cRow.append(util.getIfSetAsStr(committee, "name"))
    cRow.append(util.getIfSetAsStr(committee, "wikipedia"))
    cRow.append(util.getIfSetAsStr(committee, "jurisdiction"))
    cRow.append(util.getIfSetAsStr(committee, "jurisdiction_source"))
    cRow.append(util.getIfSetAsStr(committee, "url"))
    cRow.append(util.getIfSetAsStr(committee, "rss_url"))
    cRow.append(util.getIfSetAsStr(committee, "minority_url"))
    cRow.append(util.getIfSetAsStr(committee, "minority_rss_url"))
    cRow.append(util.getIfSetAsStr(committee, "youtube_id"))
    cRow.append(util.getIfSetAsStr(committee, "address"))
    cRow.append(util.getIfSetAsStr(committee, "phone"))
    cRow.append(committee["isCurrent"])

    return cRow



def getSubCommitteesIfSet(committee):
    if "subcommittees" in committee: return committee["subcommittees"]
    else: return []

def getCombinedCommittee(code, current, historic):
    currentSub, historicSub = [], []
    data, isCurrent = None, False

    if code in current:
        isCurrent = True
        currentSub = getSubCommitteesIfSet(current[code])
        data = current[code]
        data["names"] = {CURRENT_CONGRESS: data["name"]}
        data["congresses"] = [int(CURRENT_CONGRESS)]
        
    if code in historic:
        historicSub = getSubCommitteesIfSet(historic[code])
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
    for mem in members:
        cMember = []
        cMember.append(committee)
        cMember.append(util.getIfSetAsStr(mem, "bioguide"))
        cMember.append(util.getIfSetAsStr(mem, "party"))
        cMember.append(util.getIfSetAsStr(mem, "title"))
        cMember.append(util.getIfSetAsStr(mem, "rank"))
        membershipData.append(cMember)
    return membershipData