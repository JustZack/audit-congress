from shared import util, zjthreads, db
from datetime import datetime
from zipfile import ZipFile

#All folders that we care about in the bills folder
BILL_TYPE_FOLDERS = ["hr", "hconres", "hjres", "hres", "s", "sconres", "sjres", "sres"]

MEMBERS_MAPPING_API_URL = "http://localhost/audit-congress/api.php?route=bioguideToThomas"
MEMBERS_MAPPING = None

BILL_DATA_SCHEMA = util.readJsonFile("bill.data.schema.json")
BILL_DATA_SCHEMA_TYPES = BILL_DATA_SCHEMA.keys()

BILL_COLUMNS = ["id", "type", "number", "congress", "bioguideId", "title", "policyArea", "introduced", "updated"]
SUBJECT_COLUMNS = ["id", "billId", "type", "number", "congress", "index", "subject"]
TITLE_COLUMNS = ["id", "billId", "type", "number", "congress", "index", "title", "titleType", "titleAs", "isForPortion"]
COSPONSOR_COLUMNS = ["id", "billId", "type", "number", "congress", "bioguideId", "sponsoredAt", "withdrawnAt", "isOriginal"]
ACTION_COLUMNS = ["id", "billId", "type", "number", "congress", "index", "actionType", "text", "acted"]
SUMMARY_COLUMNS = ["id", "billId", "type", "number", "congress", "index", "text", "description", "date", "updated"]
TEXT_VERSION_COLUMNS = ["id", "billId", "type", "number", "congress", "index", "versionType", "url", "format", "date"]
COMMITTEE_COLUMNS = ["id", "billId", "type", "number", "congress", "index", "thomasId", "action", "date"]

FDSYS_XML_FILE_NAME = "fdsys_billstatus.xml"
DATA_XML_FILE_NAME = "data.xml"
DATA_JSON_FILE_NAME = "data.json"

WRITE_PARSED_BILL_FILES = False

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



def getMatchingElement(obj, path):
    tmpObj = obj
    for element in path:
        tmpObj = util.getIfSet(tmpObj, element)
        if tmpObj is None: break
    return tmpObj

def getElementWithSchema(obj, optionalPaths):
    for path in optionalPaths:
        tmpObj = getMatchingElement(obj, path)
        if tmpObj is not None: return tmpObj
    return None

def getDictOfFieldsWithSchema(rootElement, fieldValue):
    data = dict()
    for field in fieldValue.keys():
        data[field] = getFieldWithSchema(rootElement, fieldValue[field])
    return data

def getFieldWithSchema(rootElement, fieldValue):
    if type(fieldValue) is list: 
        return getElementWithSchema(rootElement, fieldValue)
    elif type(fieldValue) is dict:
        return getDictOfFieldsWithSchema(rootElement, fieldValue)
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



def saveTestBillFile(bill):
    congress = bill["bill"]["congress"]
    type_ = bill["bill"]["type"]
    number = bill["bill"]["number"]
    util.saveAsJSON("tests/{}/{}/{}.json".format(congress, type_, number), bill)
    #print("{}-{}{}".format(congress, type_, number))
    return



def ensureFieldIsList(obj, field):
    if (obj[field] is None): return []
    if type(obj[field]) is dict: return [obj[field]]
    return obj[field]

def parseBillItem(bill, fieldName, parseFunction): 
    items = []
    for item in ensureFieldIsList(bill, fieldName):
        result = parseFunction(item)
        if type(result) is list: items.extend(result)
        else: items.append(result)
    return items



def getSponsorBioguideId(sponsor, bioguideKey, thomasKey):
    if type(sponsor) is list: sponsor = sponsor[0]
    if sponsor is not None:
        return sponsor[bioguideKey] if bioguideKey in sponsor else getMemberByThomasId(sponsor[thomasKey])
    else: return None



def getTitleDict(type_, title, as_="", is_for_portion=""):
    return {"type": type_, "title": title, "as": as_, "is_for_portion": is_for_portion}   

def getCosponsorDict(id_, sponsoredAt, withdrawnAt, isOriginal=None):
    return {"id": id_, "sponsoredAt": sponsoredAt, "withdrawnAt": withdrawnAt, "isOriginal": isOriginal}

def getActionDict(type, text, actionDate):
    return {"type": type, "text": text, "actionDate": actionDate}

def getSummaryDict(text, description, date, updated = None):
    return {"text": text, "description": description, "date": date, "updated": updated}

def getTextVersionDict(versionType, url, date):
    return {"versionType": versionType, "url": url, "format": util.getFileType(url), "date": date}

def getCommitteeDict(thomasId, action, date):
    return {"thomasId": thomasId, "action": action, "date": date}


def getTitleFromXML(title):
    type_ = util.getIfSet(title, "titleType")
    title = util.getIfSet(title, "title")
    return getTitleDict(type_, title)

def getCosponsorFromXML(cosponsor):
    id_ = getSponsorBioguideId(cosponsor, "bioguideId", "thomas_id")
    since = util.getIfSet(cosponsor, "sponsorshipDate")
    withdrawn = util.getIfSet(cosponsor, "sponsorshipWithdrawnDate")
    isOriginal = util.getIfSet(cosponsor, "isOriginalCosponsor")
    return getCosponsorDict(id_, since, withdrawn, isOriginal)

def getActionFromXML(act): return getActionDict(act["type"], act["text"], act["actionDate"])

def getSummaryFromXML(sum): return getSummaryDict(sum["text"], sum["actionDesc"], sum["actionDate"], sum["updateDate"])

def getTextVersionFromXML(txt): 
    url = txt["formats"]
    url = url["item"]["url"] if url is not None else ""
    return getTextVersionDict(txt["type"], url, txt["date"])

def getCommitteeActionFromXML(thomasId, act): return getCommitteeDict(thomasId, act["name"], act["date"])

def getCommitteeFromXML(com):
    commiteeItems = []
    thomasId = com["systemCode"]
    activities = ensureFieldIsList(com["activities"], "item")
    if thomasId[4:] == "00": thomasId = thomasId[:4]
    for act in activities: commiteeItems.append(getCommitteeActionFromXML(thomasId, act))
    return commiteeItems

def getTitleFromJSON(title):
    type_ = util.getIfSet(title, "type")
    title = util.getIfSet(title, "title")
    as_ = util.getIfSet(title, "as")
    return getTitleDict(type_, title, as_)

def getCosponsorFromJSON(cosponsor):
    id_ = getSponsorBioguideId(cosponsor, "bioguide_id", "thomas_id")
    since = util.getIfSet(cosponsor, "sponsored_at")
    withdrawn = util.getIfSet(cosponsor, "withdrawn_at")
    return getCosponsorDict(id_, since, withdrawn)

def getActionFromJSON(act): return getActionDict(act["type"], act["text"], act["acted_at"])

def getSummaryFromJSON(sum): return getSummaryDict(sum["text"], sum["as"], sum["date"])



def parseBillFDSYSXml(fileData):
    xmlData = util.getParsedXmlFile(fileData)
    bill = parseBillWithSchema(xmlData, "xml")

    bill["bill"]["bioguideId"] = getSponsorBioguideId(bill["bill"]["sponsor"], "bioguideId", "thomas_id")
    
    bill["titles"] =     parseBillItem(bill, "titles", getTitleFromXML)
    bill["subjects"] =   parseBillItem(bill, "subjects", lambda s: s["name"])
    bill["cosponsors"] = parseBillItem(bill, "cosponsors", getCosponsorFromXML)
    bill["actions"] =    parseBillItem(bill, "actions", getActionFromXML)
    bill["summaries"] =  parseBillItem(bill, "summaries", getSummaryFromXML)
    bill["committees"] = parseBillItem(bill, "committees", getCommitteeFromXML)
    bill["amendments"] =    ensureFieldIsList(bill, "amendments")
    bill["laws"] =          ensureFieldIsList(bill, "laws")
    bill["textVersions"] =  parseBillItem(bill, "textVersions", getTextVersionFromXML)
    bill["relatedBills"] =      ensureFieldIsList(bill, "relatedBills")
    bill["committeeReports"] =  ensureFieldIsList(bill, "committeeReports")
    bill["cboCostEstimates"] =  ensureFieldIsList(bill, "cboCostEstimates")
    
    return bill

#There can be data.xml's available too, but seemingly only when fdsysxml is too. Not implemented unless needed.
def parseBillDataXml(fileData): raise Exception("data.xml is not implemented")

def parseBillDataJson(fileData):
    jsonData = util.getParsedJsonFile(fileData)
    bill = parseBillWithSchema(jsonData, "json")
    
    bill["bill"]["bioguideId"] = getSponsorBioguideId(bill["bill"]["sponsor"], "bioguide_id", "thomas_id")
    
    bill["titles"] =        parseBillItem(bill, "titles", getTitleFromJSON)
    bill["subjects"] =      ensureFieldIsList(bill, "subjects")
    bill["cosponsors"] =    parseBillItem(bill, "cosponsors", getCosponsorFromJSON)
    bill["actions"] =       parseBillItem(bill, "actions", getActionFromJSON)
    bill["summaries"] =     parseBillItem(bill, "summaries", getSummaryFromJSON)
    bill["committees"] =        ensureFieldIsList(bill, "committees")
    bill["amendments"] =        ensureFieldIsList(bill, "amendments")
    bill["laws"] =              ensureFieldIsList(bill, "laws")
    bill["textVersions"] =      ensureFieldIsList(bill, "textVersions")
    bill["relatedBills"] =      ensureFieldIsList(bill, "relatedBills")
    bill["committeeReports"] =  ensureFieldIsList(bill, "committeeReports")
    bill["cboCostEstimates"] =  ensureFieldIsList(bill, "cboCostEstimates")
    return bill     



def getBillObjectId(typ, number, congress, index=None):
    id_ = "{}{}-{}".format(typ, number, congress)
    if index is not None: id_ += "-{}".format(index)
    return id_

def getRows(bid, items, tnc, fieldList = None):
    i, rows = 0, []
    for item in items: 
        rid = getBillObjectId(*tnc, i)
        row = [rid, bid, *tnc, i]
        if fieldList is None: row.append(item)
        else: row.extend(util.getFields(item, fieldList))
        rows.append(row)
        i += 1
    return rows

def getBillRow(bid, bill, tnc):
    row = [bid, *tnc]
    row.extend(util.getFields(bill, ["bioguideId", "title", "policyArea", "introduced_at", "updated_at"]))
    return row

def getSubjectRows(bid, subjects, tnc):
    return getRows(bid, subjects, tnc)

def getTitleRows(bid, titles, tnc):
    return getRows(bid, titles, tnc, ["title", "type", "as", "is_for_portion"])

def getCoSponsorRows(bid, cosponsors, tnc):
    return getRows(bid, cosponsors, tnc, ["id", "sponsoredAt", "withdrawnAt", "isOriginal"])

def getActionRows(bid, actions, tnc):
    return getRows(bid, actions, tnc, ["type", "text", "actionDate"])

def getSummaryRows(bid, summaries, tnc):
    return getRows(bid, summaries, tnc, ["text", "description", "date", "updated"])

def getTextVersionRows(bid, textVersions, tnc):
    return getRows(bid, textVersions, tnc, ["versionType", "url", "format", "date"])



def splitBillsIntoTableRows(bills):
    billData,subjectData,titleData,cosponData = [],[],[],[]
    actionData,summaryData,versionData = [],[],[]

    for parsedBill in bills:
        bill = parsedBill["bill"]
        tnc = (bill["type"].lower(),bill["number"],bill["congress"])
        bid = getBillObjectId(*tnc)
        #print("{}, {} {}".format(*tcn))
        billData.append(getBillRow(bid, bill, tnc))
        subjectData.extend(getSubjectRows(bid, parsedBill["subjects"], tnc))
        titleData.extend(getTitleRows(bid, parsedBill["titles"], tnc))
        cosponData.extend(getCoSponsorRows(bid, parsedBill["cosponsors"], tnc))
        actionData.extend(getActionRows(bid, parsedBill["actions"], tnc))
        summaryData.extend(getSummaryRows(bid, parsedBill["summaries"], tnc))
        versionData.extend(getTextVersionRows(bid, parsedBill["textVersions"], tnc))
    return {"Bills": billData, "BillSubjects": subjectData, "BillTitles": titleData, 
            "BillActions": actionData, "BillSummaries": summaryData, "BillCoSponsors": cosponData,
            "BillTextVersions": versionData}

def getInsertThreads(bills):
    billToTables = splitBillsIntoTableRows(bills)
    threads = []
    threads.append(zjthreads.buildThread(db.insertRows, "Bills", BILL_COLUMNS, billToTables["Bills"]))
    threads.append(zjthreads.buildThread(db.insertRows, "BillSubjects", SUBJECT_COLUMNS, billToTables["BillSubjects"]))
    threads.append(zjthreads.buildThread(db.insertRows, "BillTitles", TITLE_COLUMNS, billToTables["BillTitles"]))
    threads.append(zjthreads.buildThread(db.insertRows, "BillActions", ACTION_COLUMNS, billToTables["BillActions"]))
    threads.append(zjthreads.buildThread(db.insertRows, "BillSummaries", SUMMARY_COLUMNS, billToTables["BillSummaries"]))
    threads.append(zjthreads.buildThread(db.insertRows, "BillTextVersions", TEXT_VERSION_COLUMNS, billToTables["BillTextVersions"]))
    threads.append(zjthreads.buildThread(db.insertRows, "BillCoSponsors", COSPONSOR_COLUMNS, billToTables["BillCoSponsors"]))
    return threads

def readBillFileFromZip(zipFile, name, path):
    file, bill = None, None
    if FDSYS_XML_FILE_NAME in path: 
        file = name+FDSYS_XML_FILE_NAME
        bill = parseBillFDSYSXml(zipFile.read(file))
    #elif DATA_XML_FILE_NAME in folder: 
    #   file = name+DATA_XML_FILE_NAME
    #   bill = parseBillDataXml(zipFile.read(file))
    elif DATA_JSON_FILE_NAME in path: 
        file = name+DATA_JSON_FILE_NAME
        bill = parseBillDataJson(zipFile.read(file))

    if WRITE_PARSED_BILL_FILES: saveTestBillFile(bill)
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