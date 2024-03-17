import sys, os, time
sys.path.append(os.path.abspath("../"))

import threading
from concurrent.futures import ThreadPoolExecutor


#Build threads given the target and arguments, start, and join them
def buildThread(target, *args):
    return threading.Thread(target=target, args=args, daemon=True)

def getThreads(function, allOptions):
    threads = []
    for op in allOptions:
        thread = buildThread(function, op)
        threads.append(thread)
    return threads

def startThreads(threads): 
    for thread in threads:
        thread.start() 

def joinThreads(threads): 
    numAlive = len(threads)
    while numAlive > 0:
        numAlive = 0
        for thread in threads:
            thread.join(2)
            if thread.is_alive(): numAlive += 1
        time.sleep(1)    
        
def startThenJoinThreads(threads):
    startThreads(threads)
    joinThreads(threads)


