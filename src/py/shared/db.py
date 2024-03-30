import os, time, shutil, io, json

import mysql.connector

import sys, os
sys.path.append(os.path.abspath("../"))

from shared import util, zjthreads, logger

VALIDATE_DB_API_URL = "http://localhost/audit-congress/src/api/api.php?route=validateSchema"

TRUNCATE_SQL = "TRUNCATE {}"
DELETE_SQL = "DELETE FROM {} WHERE {} = {}"
THREADED_DELETE = False

COUNT_SQL = "SELECT COUNT(*) FROM {}"
COUNT_WHERE_SQL = "SELECT COUNT(*) FROM {} WHERE {} = {}"

INSERT_SQL = "INSERT INTO {} ({}) VALUES ({})"\

# Opens a connection with a MySQL host
def mysql_connect():
    return mysql.connector.connect(host="127.0.0.1", user="AuditCongress", password="?6n78$y\"\"~'Fvdy", database="auditcongress")

# Executes a single query string
def mysql_execute_query(mysql_conn, sql, use_database):
    mysql_cursor = mysql_conn.cursor()
    if use_database is not None:
        mysql_cursor.execute("USE "+use_database)

    mysql_cursor.execute(sql)

    result = mysql_cursor.fetchall()

    mysql_cursor.close()
    return result

# Executes Many querys, based on executeMany. Best for inserts.
def mysql_execute_many_querys(mysql_conn, sql, data, database):
    mysql_cursor = mysql_conn.cursor()

    if database is not None:
        mysql_cursor.execute("USE "+database)

    mysql_cursor.executemany(sql, data)

    mysql_conn.commit()
    result = mysql_cursor.fetchall()
    mysql_cursor.close()
    return result

def runCommitingSql(sql, data=None):
    mysql_conn = mysql_connect()
    if data is None:
        mysql_execute_query(mysql_conn, sql, "auditcongress")
    else:
        mysql_execute_many_querys(mysql_conn, sql, data, "auditcongress")
    mysql_conn.commit()
    mysql_conn.close()

def runReturningSql(sql):
    mysql_conn = mysql_connect()
    result = mysql_execute_query(mysql_conn, sql, "auditcongress")
    mysql_conn.close()
    return result

def getInsertSql(tableName, columnsArray):
    columns = util.csvStr(columnsArray)
    valueFormat = util.csvStr(["%s" for c in columnsArray])
    return INSERT_SQL.format(tableName, columns, valueFormat)

def insertRows(tableName, columnsArray, valueData): 
    runCommitingSql(getInsertSql(tableName, columnsArray), valueData)

def insertRow(tableName, columnsArray, values): 
    insertRows(tableName, columnsArray, [values])

def deleteRowsFromTables(tables, whereCol=None, whereVal=None):
    if THREADED_DELETE:
        args = [(table, whereCol, whereVal) for table in tables]
        zjthreads.runThreads(deleteRows, args)
    else:
        for table in tables: deleteRows(table, whereCol, whereVal)

def deleteRows(tableName, whereCol=None, whereVal=None):
    sql = ""

    if None not in {whereVal, whereCol}: sql = DELETE_SQL.format(tableName, whereCol, whereVal)
    else: sql = TRUNCATE_SQL.format(tableName, whereCol, whereVal)
    runCommitingSql(sql)

def countRows(tableName, whereCol=None, whereVal=None): 
    sql = ""

    if None not in {whereVal, whereCol}: sql = COUNT_WHERE_SQL.format(tableName, whereCol, congress)
    else: sql = COUNT_SQL.format(tableName)

    count = runReturningSql(sql)[0][0]
    return count

def schemaIsValid(): return "valid" in util.getParsedJson(VALIDATE_DB_API_URL)

def throwIfShemaInvalid():
    #Make sure the DB schema is valid first
    if schemaIsValid(): logger.logInfo("Confirmed DB Schema is valid via the API.")
    else: raise Exception("Could not validate the DB schema via API. Exiting.")