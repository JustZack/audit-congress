import os, math, json
from datetime import datetime

import requests as rq
from bs4 import BeautifulSoup
import xmltodict as xml2d


def seconds_since(a): return (datetime.now()-a).total_seconds()

def countFiles(inDir):
    count = 0
    for root_dir, cur_dir, files in os.walk(inDir): count += len(files)
    return count

def ensureFoldersExist(path): os.makedirs(os.path.dirname(path), exist_ok=True)

def saveFileAny(writeType, path, data):
    ensureFoldersExist(path)
    with open(path, writeType) as file: file.write(data)
    
def saveBinaryFile(path, data): saveFileAny("wb", path, data)
def saveFile(path, data): saveFileAny("w", path, data)

def deleteFiles(fileList): [os.remove(f) for f in fileList]

def get(url): return rq.get(url).content

def getParsedSoup(url, features="html.parser"):
    soup = BeautifulSoup(get(url), features)
    return soup
def getHtmlSoup(url): return getParsedSoup(url)
def getXmlSoup(url): return getParsedSoup(url, "xml")

def getParsedJson(url): return getParsedJsonFile(get(url))
def getParsedJsonFile(fileData): return json.loads(fileData)

def getParsedXml(url): return getParsedXmlFile(get(url))
def getParsedXmlFile(fileData): return xml2d.parse(fileData)

def downloadZipFile(url, savePath): saveBinaryFile(savePath, get(url))

def chunkList(array, chunkSize):
    chunckedList = []
    numChunks = math.ceil(len(array)/chunkSize)
    for n in range(numChunks):
        start = n*chunkSize
        end = (n+1)*chunkSize
        chunckedList.append(array[start:end])
    return chunckedList

def csvStr(itemArray): return ",".join(itemArray)