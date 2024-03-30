import time
from shared import util

def appendMemberUpdateTimes(row):
    now = time.time()
    row.append(now)
    row.append(now + (60*60*24*7))
    return row



def getMemberRow(member, isCurrent):
    mRow = []
    mId, mName, mBio = member["id"], member["name"], member["bio"]
    mRow.append(mId["bioguide"])
    mRow.append(util.getFieldIfExists(mId, "thomas"))
    mRow.append(util.getFieldIfExists(mId, "lis"))
    mRow.append(util.getFieldIfExists(mId, "govtrack"))
    mRow.append(util.getFieldIfExists(mId, "opensecrets"))
    mRow.append(util.getFieldIfExists(mId, "votesmart"))
    mRow.append(util.getFieldIfExists(mId, "cspan"))
    mRow.append(util.getFieldIfExists(mId, "maplight"))
    mRow.append(util.getFieldIfExists(mId, "icpsr"))
    mRow.append(util.getFieldIfExists(mId, "wikidata"))
    mRow.append(util.getFieldIfExists(mId, "google_entity_id"))

    mRow.append(util.getFieldIfExists(mName, "official_full"))
    mRow.append(util.getFieldIfExists(mName, "first"))
    mRow.append(util.getFieldIfExists(mName, "last"))

    mRow.append(util.getFieldIfExists(mBio, "gender"))
    mRow.append(util.getFieldIfExists(mBio, "birthday"))

    mRow.append(isCurrent)
    
    return appendMemberUpdateTimes(mRow)

def getTermRows(terms, bioguideId):
    mTerms = []
    for term in terms:
        mTerm = []
        mTerm.append(bioguideId)
        mTerm.append(util.getFieldIfExists(term, "type"))
        mTerm.append(util.getFieldIfExists(term, "start"))
        mTerm.append(util.getFieldIfExists(term, "end"))
        mTerm.append(util.getFieldIfExists(term, "state"))
        mTerm.append(util.getFieldIfExists(term, "district"))
        mTerm.append(util.getFieldIfExists(term, "party"))
        mTerm.append(util.getFieldIfExists(term, "class"))
        mTerm.append(util.getFieldIfExists(term, "how"))
        mTerm.append(util.getFieldIfExists(term, "state_rank"))
        mTerm.append(util.getFieldIfExists(term, "url"))
        mTerm.append(util.getFieldIfExists(term, "rss_url"))
        mTerm.append(util.getFieldIfExists(term, "contact_form"))
        mTerm.append(util.getFieldIfExists(term, "address"))
        mTerm.append(util.getFieldIfExists(term, "office"))
        mTerm.append(util.getFieldIfExists(term, "phone"))

        mTerms.append(appendMemberUpdateTimes(mTerm))
    return mTerms

def getElectionRows(elections, bioguideId):
    mElections = []
    for election in elections:
        mElection = []
        mElection.append(election)
        mElection.append(bioguideId)

        mElections.append(appendMemberUpdateTimes(mElection))
    return mElections



def getSocialRow(social):
    sRow = []
    sId, sSocial = social["id"], social["social"]

    sRow.append(sId["bioguide"])
    sRow.append(util.getFieldIfExists(sSocial, "twitter"))
    sRow.append(util.getFieldIfExists(sSocial, "twitter_id"))
    sRow.append(util.getFieldIfExists(sSocial, "facebook"))
    sRow.append(util.getFieldIfExists(sSocial, "facebook_id"))
    sRow.append(util.getFieldIfExists(sSocial, "youtube"))
    sRow.append(util.getFieldIfExists(sSocial, "youtube_id"))
    sRow.append(util.getFieldIfExists(sSocial, "instagram"))
    sRow.append(util.getFieldIfExists(sSocial, "instagram_id"))

    return appendMemberUpdateTimes(sRow)



def getOfficeRows(bioguideId, offices):
    mOffices = []
    for office in offices:
        mOffice = []

        mOffice.append(util.getFieldIfExists(office, "id"))
        mOffice.append(bioguideId)
        mOffice.append(util.getFieldIfExists(office, "address"))
        mOffice.append(util.getFieldIfExists(office, "suite"))
        mOffice.append(util.getFieldIfExists(office, "building"))
        mOffice.append(util.getFieldIfExists(office, "city"))
        mOffice.append(util.getFieldIfExists(office, "state"))
        mOffice.append(util.getFieldIfExists(office, "zip"))
        mOffice.append(util.getFieldIfExists(office, "latitude"))
        mOffice.append(util.getFieldIfExists(office, "longitude"))
        mOffice.append(util.getFieldIfExists(office, "phone"))
        mOffice.append(util.getFieldIfExists(office, "fax"))

        mOffices.append(appendMemberUpdateTimes(mOffice))
    return mOffices


def getSubCommitteeRows(subcommitees, parentId, parentType):
    subComData = []
    for sub in subcommitees: 
        subCom = subcommitees[sub]
        subCom["type"] = parentType
        subCom["parent_id"] = parentId
        subCom["thomas_id"] = parentId+subCom["thomas_id"]
        subComData.append(getCommitteeRow(subCom))
    return subComData

def getCommitteeRow(committee):
    cRow = []

    cRow.append(committee["thomas_id"])
    cRow.append(util.getFieldIfExists(committee, "parent_id"))
    cRow.append(util.getFieldIfExists(committee, "type"))
    cRow.append(util.getFieldIfExists(committee, "name"))
    cRow.append(util.getFieldIfExists(committee, "wikipedia"))
    cRow.append(util.getFieldIfExists(committee, "jurisdiction"))
    cRow.append(util.getFieldIfExists(committee, "jurisdiction_source"))
    cRow.append(util.getFieldIfExists(committee, "url"))
    cRow.append(util.getFieldIfExists(committee, "rss_url"))
    cRow.append(util.getFieldIfExists(committee, "minority_url"))
    cRow.append(util.getFieldIfExists(committee, "minority_rss_url"))
    cRow.append(util.getFieldIfExists(committee, "youtube_id"))
    cRow.append(util.getFieldIfExists(committee, "address"))
    cRow.append(util.getFieldIfExists(committee, "phone"))
    cRow.append(committee["isCurrent"])

    return cRow

def getSubCommitteesIfSet(committee):
    if "subcommittees" in committee: return committee["subcommittees"]
    else: return []


def getAggregatedCommittees(current, historic):
    aggregated = dict()
    cSet = set(current.keys())
    cSet.update(historic.keys())

    for code in cSet:
        currentSub, historicSub = [], []
        isCurrent = False
        data = None
        if code in current:
            currentSub = getSubCommitteesIfSet(current[code])
            isCurrent = True
            data = current[code]
            
        if code in historic:
            historicSub = getSubCommitteesIfSet(historic[code])
            if isCurrent:
                data["names"] = historic[code]["names"]
                data["congresses"] = historic[code]["congresses"]
            else: 
                data = historic[code]
            
        
        if len(currentSub) > 0 or len(historicSub) > 0:
            subCurrent = util.dictArrayToDict(currentSub, "thomas_id")
            subHistoric = util.dictArrayToDict(historicSub, "thomas_id")
            data["subcommittees"] = getAggregatedCommittees(subCurrent, subHistoric)

        data["isCurrent"] = isCurrent
        aggregated[code] = data
    return aggregated


def getMembersipRows(members, committee):
    membershipData = []
    for mem in members:
        cMember = []
        cMember.append(committee)
        cMember.append(util.getFieldIfExists(mem, "bioguide"))
        cMember.append(util.getFieldIfExists(mem, "party"))
        cMember.append(util.getFieldIfExists(mem, "title"))
        cMember.append(util.getFieldIfExists(mem, "rank"))
        membershipData.append(cMember)
    return membershipData