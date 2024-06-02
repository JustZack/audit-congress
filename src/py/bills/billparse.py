from shared import util, zjthreads, db
from datetime import datetime
from zipfile import ZipFile

#All folders that we care about in the bills folder
BILL_TYPE_FOLDERS = ["hr", "hconres", "hjres", "hres", "s", "sconres", "sjres", "sres"]

MEMBERS_MAPPING_API_URL = "http://localhost/audit-congress/api.php?route=bioguideToThomas"
MEMBERS_MAPPING = None

BILL_DATA_SCHEMA = util.readJsonFile("bill.data.schema.json")
BILL_DATA_SCHEMA_TYPES = BILL_DATA_SCHEMA.keys()

BILL_COLUMNS = ["id", "type", "congress", "number", "bioguideId", "title", "policyArea", "introduced", "updated"]
SUBJECT_COLUMNS = ["id", "billId", "type", "congress", "number", "index", "subject"]
TITLE_COLUMNS = ["id", "billId", "type", "congress", "number", "index", "title", "titleType", "titleAs", "isForPortion"]
COSPONSOR_COLUMNS = ["id", "billId", "type", "congress", "number", "bioguideId", "sponsoredAt", "withdrawnAt", "isOriginal"]
ACTION_COLUMNS = ["id", "billId", "index", "type", "text", "acted"]

FDSYS_XML_FILE_NAME = "fdsys_billstatus.xml"
DATA_XML_FILE_NAME = "data.xml"
DATA_JSON_FILE_NAME = "data.xml"

def fetchMemberMapping():
    global MEMBERS_MAPPING
    resp = util.getParsedJson(MEMBERS_MAPPING_API_URL)
    if "bioguideToThomas" in resp:
        MEMBERS_MAPPING =  resp["bioguideToThomas"]
        return True
    else:
        return False

def getMemberByThomasId(thomasId):
    global MEMBERS_MAPPING
    try: return MEMBERS_MAPPING[thomasId]
    except Exception as e: return None



def getElementWithSchema(obj, optionalPaths):
    for path in optionalPaths:
        tmpObj = obj
        for element in path:
            if tmpObj is not None and element in tmpObj.keys(): 
                tmpObj = tmpObj[element]
            else: 
                tmpObj = None
                break
        if tmpObj is not None: return tmpObj
    return None

def getFieldWithSchema(rootElement, fieldValue):
    if type(fieldValue) is list: 
        return getElementWithSchema(rootElement, fieldValue)
    elif type(fieldValue) is dict:
        data = dict()
        for field in fieldValue.keys():
            data[field] = getFieldWithSchema(rootElement, fieldValue[field])
        return data
    else: return None

def parseBillWithSchema(bill, schemaType):
    if schemaType not in BILL_DATA_SCHEMA_TYPES:
        raise Exception("bill.parse: invalid schema type provided: {}. use one of {}"
                        .format(schemaType, BILL_DATA_SCHEMA_TYPES))
    
    schema = BILL_DATA_SCHEMA[schemaType]
    root = getElementWithSchema(bill, schema["root"]) if schema["root"] is not None else bill
    
    data = dict()
    fields = schema["fields"].keys()
    for field in fields:
        fieldValue = schema["fields"][field]
        data[field] = getFieldWithSchema(root, fieldValue)
    return data

def getIfSet(key, dct, defaultValue = None): 
    return dct[key] if key in dct else defaultValue



def saveTestBillFile(bill):
    util.saveAsJSON("tests/{}/{}/{}.json".format(bill["bill"]["congress"], 
                                                 bill["bill"]["type"], 
                                                 bill["bill"]["number"]), bill)
    #print("{}-{}{}".format(cong, typ, num))



def ensureFieldIsList(obj, field):
    if (obj[field] is None): return []
    if type(obj[field]) is dict: return [obj[field]]
    return obj[field]



def getSponsorBioguideId(sponsor, bioguideKey, thomasKey):
    if sponsor is not None:
        return sponsor[bioguideKey] if bioguideKey in sponsor else getMemberByThomasId(sponsor[thomasKey])
    else: return None

def getCosponsorDict(id, sponsoredAt, withdrawnAt, isOriginal):
    return {"id": id, "sponsoredAt": sponsoredAt, "withdrawnAt": withdrawnAt, "isOriginal": isOriginal}

def getActionDict(type, text, actionDate):
    return {"type": type, "text": text, "actionDate": actionDate}

def getTitleFromXML(title):
    return {"type": title["titleType"], "title": title["title"], "as": "", "is_for_portion": ""}

def getCosponsorFromXML(cosponsor):
    id = getSponsorBioguideId(cosponsor, "bioguideId", "thomas_id")
    since = cosponsor["sponsorshipDate"]
    withdrawn = getIfSet("sponsorshipWithdrawnDate", cosponsor)
    isOriginal = getIfSet("isOriginalCosponsor", cosponsor)
    return getCosponsorDict(id, since, withdrawn, isOriginal)

def getActionFromXML(act): return getActionDict(act["type"], act["text"], act["actionDate"])

def getCosponsorFromJSON(cosponsor):
    id = getSponsorBioguideId(cosponsor, "bioguide_id", "thomas_id")
    since = cosponsor["sponsored_at"]
    withdrawn = getIfSet("withdrawn_at", cosponsor)
    isOriginal = None
    return getCosponsorDict(id, since, withdrawn, isOriginal)

def getActionFromJSON(act): return getActionDict(act["type"], act["text"], act["acted_at"])



def parseBillFDSYSXml(fileData):
    xmlData = util.getParsedXmlFile(fileData)
    bill = parseBillWithSchema(xmlData, "xml")

    sponsor = bill["bill"]["sponsor"]
    if type(sponsor) is list: sponsor = sponsor[0]
    bill["bill"]["bioguideId"] = getSponsorBioguideId(sponsor, "bioguideId", "thomas_id")
    
    bill["titles"] =     [getTitleFromXML(title) for title in ensureFieldIsList(bill, "titles")]
    bill["subjects"] =   [subject["name"] for subject in ensureFieldIsList(bill, "subjects")]
    bill["cosponsors"] = [getCosponsorFromXML(cosponsor) for cosponsor in ensureFieldIsList(bill, "cosponsors")]
    bill["actions"] =    [getActionFromXML(action) for action in ensureFieldIsList(bill, "actions")]
    bill["summaries"] =         ensureFieldIsList(bill, "summaries")
    bill["committees"] =        ensureFieldIsList(bill, "committees")
    bill["amendments"] =        ensureFieldIsList(bill, "amendments")
    bill["laws"] =              ensureFieldIsList(bill, "laws")
    bill["textVersions"] =      ensureFieldIsList(bill, "textVersions")
    bill["relatedBills"] =      ensureFieldIsList(bill, "relatedBills")
    bill["committeeReports"] =  ensureFieldIsList(bill, "committeeReports")
    bill["cboCostEstimates"] =  ensureFieldIsList(bill, "cboCostEstimates")

    saveTestBillFile(bill)
    return bill

#There can be data.xml's available too, but seemingly only when fdsysxml is too. Not implemented unless needed.
def parseBillDataXml(fileData): raise Exception("data.xml is not implemented")

def parseBillDataJson(fileData):
    jsonData = util.getParsedJsonFile(fileData)
    bill = parseBillWithSchema(jsonData, "json")
    
    sponsor = bill["bill"]["sponsor"]
    if type(sponsor) is list: sponsor = sponsor[0]
    bill["bill"]["bioguideId"] = getSponsorBioguideId(sponsor, "bioguide_id", "thomas_id")
    
    bill["titles"] =        ensureFieldIsList(bill, "titles")
    bill["subjects"] =      ensureFieldIsList(bill, "subjects")
    bill["cosponsors"] =    [getCosponsorFromJSON(cosponsor) for cosponsor in ensureFieldIsList(bill, "cosponsors")]
    bill["actions"] =       [getActionFromJSON(action) for action in ensureFieldIsList(bill, "actions")]
    bill["summaries"] =         ensureFieldIsList(bill, "summaries")
    bill["committees"] =        ensureFieldIsList(bill, "committees")
    bill["amendments"] =        ensureFieldIsList(bill, "amendments")
    bill["laws"] =              ensureFieldIsList(bill, "laws")
    bill["textVersions"] =      ensureFieldIsList(bill, "textVersions")
    bill["relatedBills"] =      ensureFieldIsList(bill, "relatedBills")
    bill["committeeReports"] =  ensureFieldIsList(bill, "committeeReports")
    bill["cboCostEstimates"] =  ensureFieldIsList(bill, "cboCostEstimates")

    saveTestBillFile(bill)
    return bill     



def getBillObjectId(typ, number, congress, index=None):
    id = "{}{}-{}".format(typ, number, congress)
    if index is not None: id += "-{}".format(index)
    return id

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

def getActionRows(bid, actions, t, n, c):
    i, acts = 0, []
    for action in actions:
        aid = getBillObjectId(t, n, c, i)
        acts.append((aid, bid, i, action["type"], action["text"], action["actionDate"]))
        i += 1
    return acts



def splitBillsIntoTableRows(bills):
    billData,subjectData,titleData,cosponData,actionData = [],[],[],[],[]

    for parsedBill in bills:
        bill = parsedBill["bill"]
        tcn = (bill["type"].lower(),bill["congress"],bill["number"])
        bioguide = bill["bioguideId"]
        bid = getBillObjectId(*tcn)
        
        billData.append((bid, *tcn, bioguide, bill["title"], bill["policyArea"], bill["introduced_at"], bill["updated_at"]))
        subjectData.extend(getSubjectRows(bid, parsedBill["subjects"], *tcn))
        titleData.extend(getTitleRows(bid, parsedBill["titles"], *tcn))
        cosponData.extend(getCoSponsorRows(bid, parsedBill["cosponsors"], *tcn))
        #print("{}, {} {}".format(*tcn))
        actionData.extend(getActionRows(bid, parsedBill["actions"], *tcn))
    return {"Bills": billData, "BillSubjects": subjectData, "BillTitles": titleData, 
            "BillActions": actionData, "BillCoSponsors": cosponData}

def getInsertThreads(bills):
    billToTables = splitBillsIntoTableRows(bills)
    threads = []
    threads.append(zjthreads.buildThread(db.insertRows, "Bills", BILL_COLUMNS, billToTables["Bills"]))
    threads.append(zjthreads.buildThread(db.insertRows, "BillSubjects", SUBJECT_COLUMNS, billToTables["BillSubjects"]))
    threads.append(zjthreads.buildThread(db.insertRows, "BillTitles", TITLE_COLUMNS, billToTables["BillTitles"]))
    threads.append(zjthreads.buildThread(db.insertRows, "BillCoSponsors", COSPONSOR_COLUMNS, billToTables["BillCoSponsors"]))
    threads.append(zjthreads.buildThread(db.insertRows, "BillCoSponsors", ACTION_COLUMNS, billToTables["BillActions"]))
    return threads

def readBillFileFromZip(zipFile, name,path):
    file = None
    if FDSYS_XML_FILE_NAME in path: file = name+FDSYS_XML_FILE_NAME
    #elif DATA_XML_FILE_NAME in folder: file = name+DATA_XML_FILE_NAME
    elif DATA_JSON_FILE_NAME in path: file = name+DATA_JSON_FILE_NAME

    if file is None: return None

    data, bill = zipFile.read(file), None
    #Prefer fdsys.xml files, they have way more info available
    if FDSYS_XML_FILE_NAME in file: bill = parseBillFDSYSXml(data)
    #elif DATA_XML_FILE_NAME IN file: bill = parseBillDataXml(data)
    elif DATA_JSON_FILE_NAME in file: bill = parseBillDataJson(data)

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