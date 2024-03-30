import sys, os, time
sys.path.append(os.path.abspath("../"))

import threading
from concurrent.futures import ThreadPoolExecutor


#Build threads given the target and arguments, start, and join them
def buildThread(target, *args):
    return threading.Thread(target=target, args=args, daemon=True)

def getThreads(function, arguments):
    threads = []
    for args in arguments:
        thread = buildThread(function, args)
        threads.append(thread)
    return threads

def startThreads(threads): 
    for thread in threads:
        thread.start() 

def getLivingThreads(threads):
    return [t for t in threads if t.is_alive()]

def joinThreads(threads): 
    numAlive = len(threads)
    while numAlive > 0:
        numAlive = len(getLivingThreads(threads))
        time.sleep(1/100)      

def startThenJoinThreads(threads):
    startThreads(threads)
    joinThreads(threads)


def runThreads(function, arguments):
    threads = getThreads(function, arguments)
    startThenJoinThreads(threads)

def runThreadPool(function, arguments, size = 10):
    with ThreadPoolExecutor(size) as exe:
        for a in arguments:
            exe.submit(function, arguments)
