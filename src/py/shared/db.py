import os, time, shutil, io, json

import mysql.connector

import threading
from concurrent.futures import ThreadPoolExecutor

VALIDATE_DB_API_URL = "http://localhost/audit-congress/src/api/api.php?route=validateSchema"

# Opens a connection with a MySQL host
def mysql_connect():
    return mysql.connector.connect(host="127.0.0.1", user="AuditCongress", password="?6n78$y\"\"~'Fvdy", database="auditcongress")

# Executes a single query string
def mysql_execute_query(mysql_conn, sql, use_database):
    mysql_cursor = mysql_conn.cursor()
    if use_database is not None:
        mysql_cursor.execute("USE "+use_database)

    mysql_cursor.execute(sql)

    result = [row[0] for row in mysql_cursor.fetchall()]

    mysql_cursor.close()
    return result

# Executes Many querys, based on executeMany. Best for inserts.
def mysql_execute_many_querys(mysql_conn, sql, data, database):
    mysql_cursor = mysql_conn.cursor()

    if database is not None:
        mysql_cursor.execute("USE "+database)

    mysql_cursor.executemany(sql, data)

    mysql_conn.commit()
    result = [row[0] for row in mysql_cursor.fetchall()]
    mysql_cursor.close()
    return result

def countRows(inTable, congress=None): 
    mysql_conn = mysql_connect()
    
    sql = ""
    if congress is None: sql = "SELECT COUNT(*) FROM {}".format(inTable)
    else: sql = "SELECT COUNT(*) FROM {} WHERE congress = {}".format(inTable, congress)

    count = mysql_execute_query(mysql_conn, sql, "auditcongress")[0]
    mysql_conn.close()
    return count

def runInsertingSql(sql, data):
    mysql_conn = mysql_connect()
    mysql_execute_many_querys(mysql_conn, sql, data, "auditcongress")
    mysql_conn.commit()
    mysql_conn.close()

def runCommitingSql(sql):
    mysql_conn = mysql_connect()
    mysql_execute_query(mysql_conn, sql, "auditcongress")
    mysql_conn.commit()
    mysql_conn.close()

def schemaIsValid():
    page = rq.get(VALIDATE_DB_API_URL)
    return "valid" in json.loads(page.content)