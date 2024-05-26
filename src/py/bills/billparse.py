from shared import util, zjthreads, db
from datetime import datetime
from zipfile import ZipFile

#All folders that we care about in the bills folder
BILL_TYPE_FOLDERS = ["hr", "hconres", "hjres", "hres", "s", "sconres", "sjres", "sres"]

MEMBERS_MAPPING_API_URL = "http://localhost/audit-congress/src/api/api.php?route=bioguideToThomas"
MEMBERS_MAPPING = None

BILL_COLUMNS = ["id", "type", "congress", "number", "bioguideId", "title", "introduced", "updated"]
SUBJECT_COLUMNS = ["id", "billId", "type", "congress", "number", "subjectIndex", "subject"]
TITLE_COLUMNS = ["id", "billId", "type", "congress", "number", "titleIndex", "title", "titleType", "titleAs", "isForPortion"]
COSPONSOR_COLUMNS = ["id", "billId", "type", "congress", "number", "bioguideId", "sponsoredAt", "withdrawnAt", "isOriginal"]

def fetchMemberMapping():
    global MEMBERS_MAPPING
    resp = util.getParsedJson(MEMBERS_MAPPING_API_URL)
    if "mapping" in resp:
        MEMBERS_MAPPING =  resp["mapping"]
        return True
    else:
        return False

def getMemberByThomasId(thomasId):
    global MEMBERS_MAPPING
    try: return MEMBERS_MAPPING[thomasId]
    except Exception as e: return None



def parseBillFDSYSXml(fileData):
    xmlData = util.getParsedXmlFile(fileData)
    
    bill = xmlData["billStatus"]["bill"] 

    billData = dict()

    typ = bill["type"] if "type" in bill.keys() else bill["billType"]
    cong = bill["congress"]
    num = bill["number"] if "number" in bill.keys() else bill["billNumber"]

    actualBill = dict()
    billData["bill"] = actualBill

    actualBill["type"] = typ
    actualBill["congress"] = cong
    actualBill["number"] = num

    sponsor = bill["sponsors"]
    sponsor = sponsor["item"] if sponsor is not None else ""
    if type(sponsor) is list: sponsor = sponsor[0]
    if type(sponsor) is dict: sponsor = sponsor["bioguideId"]
    actualBill["bioguideId"] = sponsor
   
    actualBill["introduced_at"] = bill["introducedDate"]
    actualBill["updated_at"] = bill["updateDate"]

    actualBill["originChamber"] = bill["originChamber"]

    policyArea = bill["policyArea"] if "policyArea" in bill else None
    actualBill["policyArea"] = policyArea["name"] if policyArea is not None else ""
    actualBill["summaries"] = bill["summaries"] if "summaries" in bill else []
    
    subjects = bill["subjects"] if "subjects" in bill else None
    ["subjects", "billSubjects", "legislativeSubjects", "item"]
    if subjects is not None:
        if "billSubjects" in subjects: subjects = subjects["billSubjects"]
        subjects = subjects["legislativeSubjects"]
        if subjects is not None: subjects = subjects["item"]
        else: subjects = []
        
        if type(subjects) is dict: subjects = [subjects]
    else: subjects = []

    cosponsoredDat = bill["cosponsors"] if "cosponsors" in bill else None
    ["cosponsors", "item"]
    if cosponsoredDat is not None: cosponsoredDat = cosponsoredDat["item"]
    else: cosponsoredDat = []

    cosponsored = []
    if type(cosponsoredDat) is list:
        for cospon in cosponsoredDat:
            id = cospon["bioguideId"] if "bioguideId" in cospon else getMemberByThomasId(cospon["thomas_id"])
            since = cospon["sponsorshipDate"]
            withdrawn = cospon["sponsorshipWithdrawnDate"] if "sponsorshipWithdrawnDate" in cospon else None
            isOriginal = cospon["isOriginalCosponsor"] if "isOriginalCosponsor" in cospon else None
            cosponsored.append({"id": id, "sponsoredAt": since, "withdrawnAt": withdrawn, "isOriginal": isOriginal})
    elif type(cosponsoredDat) is dict:
        id = cosponsoredDat["bioguideId"] if "bioguideId" in cosponsoredDat else getMemberByThomasId(cosponsoredDat["thomas_id"])
        since = cosponsoredDat["sponsorshipDate"]
        withdrawn = cosponsoredDat["sponsorshipWithdrawnDate"] if "sponsorshipWithdrawnDate" in cosponsoredDat else None
        isOriginal = cosponsoredDat["isOriginalCosponsor"] if "isOriginalCosponsor" in cosponsoredDat else None
        cosponsored.append({"id": id, "sponsoredAt": since, "withdrawnAt": withdrawn, "isOriginal": isOriginal})


    committees = bill["committees"] if "committees" in bill else None
    ["billCommittees", "item"]
    if committees is not None:
        if "billCommittees" in committees: committees = committees["billCommittees"]
        if committees is not None and "item" in committees: committees = committees["item"]
        else: committees = []
    else: committees = []

    amendments = bill["amendments"] if "amendments" in bill else None
    ["amendments", "amendment"]
    if amendments is not None:
        if "amendment" in amendments: amendments = amendments["amendment"]
        else: amendments = []
    else: amendments = []

    actions = bill["actions"] if "actions" in bill else None
    if actions is not None:
        if "item" in actions: actions = actions["item"]
        else: actions = []
    else: actions = []

    laws = bill["laws"] if "laws" in bill else None
    if laws is not None:
        if "item" in laws: laws = laws["item"]
        else: laws = []
    else: laws = []

    titles = bill["titles"] if "titles" in bill else None
    if titles is not None:
        if "item" in titles: titles = titles["item"]
        else: titles = []
    else: titles = []

    try:    
        actualBill["title"] = bill["title"]
    except Exception:
        if len(titles) > 0: actualBill["title"] = titles[0]["title"]

    titles = [{"type": title["titleType"], "title": title["title"], "as": "", "is_for_portion": ""} for title in titles]

    billData["bill"] = actualBill
    billData["titles"] = titles
    billData["subjects"] = [subject["name"] for subject in subjects]
    billData["cosponsors"] = cosponsored
    billData["committees"] = committees
    billData["amendments"] = amendments
    billData["actions"] = actions
    billData["laws"] = laws

    return billData

def parseBillDataXml(fileData):
    raise Exception("data.xml is not implemented")

    billData = dict()
    xmlData = xmltodict.parse(fileData)
    
    actualBill = dict()
    actualBill["id"] = jsonData["bill_id"]

    billElem = xmlData.select("<bill>")
    print(billElem)
    actualBill["type"] = billElem["type"]
    actualBill["congress"] = billElem["session"]
    actualBill["number"] = billElem["number"]
    return billData
    sponsor = jsonData["sponsor"]
    if (sponsor is not None):
        if "bioguide_id" in sponsor:
            actualBill["bioguideId"] = sponsor["bioguide_id"]
        else:
            actualBill["bioguideId"] = getMemberByThomasId(sponsor["thomas_id"])
    else:
        actualBill["bioguideId"] = None

    actualBill["officialTitle"] = jsonData["official_title"]
    actualBill["popularTitle"] = jsonData["popular_title"]

    billData["bill"] = actualBill
    billData["titles"] = jsonData["titles"]
    billData["subjects"] = jsonData["subjects"]
    billData["cosponsors"] = jsonData["cosponsors"]
    billData["committees"] = jsonData["committees"]
    billData["amendments"] = jsonData["amendments"]
    billData["actions"] = jsonData["actions"]

    return billData

def parseBillDataJson(fileData):
    billData = dict()
    jsonData = util.getParsedJsonFile(fileData)

    actualBill = dict()
    
    actualBill["type"] = jsonData["bill_type"]
    actualBill["congress"] = jsonData["congress"]
    actualBill["number"] = jsonData["number"]

    sponsor = jsonData["sponsor"]
    if (sponsor is not None):
        if "bioguide_id" in sponsor:
            actualBill["bioguideId"] = sponsor["bioguide_id"]
        else:
            actualBill["bioguideId"] = getMemberByThomasId(sponsor["thomas_id"])
    else:
        actualBill["bioguideId"] = None

    actualBill["title"] = jsonData["official_title"]

    actualBill["introduced_at"] = jsonData["introduced_at"]
    actualBill["updated_at"] = jsonData["updated_at"]

    cosponsors = []
    for cospon in jsonData["cosponsors"]:
        id = cospon["bioguide_id"] if "bioguide_id" in cospon else getMemberByThomasId(cospon["thomas_id"])
        since = cospon["sponsored_at"]
        withdrawn = cospon["withdrawn_at"] if "withdrawn_at" in cospon else None
        isOriginal = None
        cosponsors.append({"id": id, "sponsoredAt": since, "withdrawnAt": withdrawn, "isOriginal": isOriginal})
    
    billData["bill"] = actualBill
    billData["titles"] = jsonData["titles"]
    billData["subjects"] = jsonData["subjects"]
    billData["cosponsors"] = cosponsors
    billData["committees"] = jsonData["committees"]
    billData["amendments"] = jsonData["amendments"]
    billData["actions"] = jsonData["actions"]
    billData["actions"] = jsonData["actions"]

    return billData



def getBillObjectId(typ, number, congress, index=None):
    if index is None: return "{}{}-{}".format(typ, number, congress)
    else: return "{}{}-{}-{}".format(typ, number, congress, index)

def getSubjectRows(bid, subjects, t, n, c):
    i, subjs = 0, []
    for subject in subjects: 
        sid = getBillObjectId(t, n, c, i)
        subjs.append((sid, bid, t, n, c, i, subject))
        i += 1
    return subjs

def getTitleRows(bid, titles, t, n, c):
    i, ttls = 0, []
    for title in titles: 
        tid = getBillObjectId(t, n, c, i)
        portion = title["is_for_portion"] if "is_for_portion" in title.keys() else ""
        ttls.append((tid, bid, t, n, c, i, title["title"], title["type"], title["as"], portion))
        i += 1
    return ttls

def getCoSponsorRows(bid, cosponsors, t, n, c):
    i, cospons = 0, []
    for cosponsor in cosponsors:
        cid = getBillObjectId(t, n, c, i)
        cospons.append((cid, bid, t, n, c, cosponsor["id"], cosponsor["sponsoredAt"], cosponsor["withdrawnAt"], cosponsor["isOriginal"]))
        i += 1
    return cospons

def splitBillsIntoTableRows(bills):
    billData,subjectData,titleData,cosponData = [],[],[],[]

    for parsedBill in bills:
        bill = parsedBill["bill"]
        tcn = (bill["type"].lower(),bill["congress"],bill["number"])
        bioguide = bill["bioguideId"]
        bid = getBillObjectId(*tcn)
        
        billData.append((bid, *tcn, bioguide, bill["title"], bill["introduced_at"], bill["updated_at"]))
        subjectData.extend(getSubjectRows(bid, parsedBill["subjects"], *tcn))
        titleData.extend(getTitleRows(bid, parsedBill["titles"], *tcn))
        cosponData.extend(getCoSponsorRows(bid, parsedBill["cosponsors"], *tcn))
    return {"Bills": billData, "BillSubjects": subjectData, "BillTitles": titleData, "BillCoSponsors": cosponData}

def getInsertThreads(bills):
    billToTables = splitBillsIntoTableRows(bills)
    threads = []
    threads.append(zjthreads.buildThread(db.insertRows, "Bills", BILL_COLUMNS, billToTables["Bills"]))
    threads.append(zjthreads.buildThread(db.insertRows, "BillSubjects", SUBJECT_COLUMNS, billToTables["BillSubjects"]))
    threads.append(zjthreads.buildThread(db.insertRows, "BillTitles", TITLE_COLUMNS, billToTables["BillTitles"]))
    threads.append(zjthreads.buildThread(db.insertRows, "BillCoSponsors", COSPONSOR_COLUMNS, billToTables["BillCoSponsors"]))
    return threads

def readBillFileFromZip(zipFile, name,path):
    file = None
    if "data.json" in path: file = name+"data.json"
    elif "fdsys_billstatus.xml" in path: file = name+"fdsys_billstatus.xml"
    #elif "data.xml" in folder: file = name+"data.xml"

    if file is None: return None

    data, bill = zipFile.read(file), None
    if "data.json" in file: bill = parseBillDataJson(data)
    elif "fdsys_billstatus.xml" in file: bill = parseBillFDSYSXml(data)
    #elif file.find("data.xml") >= 0: bill = parseBillDataXml(data)

    return bill

def getBillItemsByFolder(fileList):
    folders = dict()
    for path in fileList:
        #Do not process amendents... yet
        if "amendments" in path: continue
        #Get the directory from the file path
        directory = util.getPathDirectory(path)
    
        #Add directory if unset
        if directory not in folders: folders[directory] = set()
        #Append files to this directory listing
        if util.pathIsFile(path): folders[directory].add(util.getPathFile(path))
    return folders

def readZippedFiles(zipFile):
    bills, files = [], zipFile.namelist()
    folderDict, skippedFiles = getBillItemsByFolder(files), 0

    for name,path in folderDict.items():
        #Skip folders without files
        if path is set(): continue
        bill = readBillFileFromZip(zipFile, name, path)
        
        if bill is None: skippedFiles += 1
        else: bills.append(bill)

    #if skippedFiles > 0: print("Skipped",skippedFiles,"fdsys_billstatus.xml files") 
    return bills

def parseBills(zipFile):
    with ZipFile(zipFile, 'r') as zipped: 
        bills = readZippedFiles(zipped)
    return bills