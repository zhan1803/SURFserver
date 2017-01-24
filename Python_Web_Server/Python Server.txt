'''
Created on Jun 14, 2016

@author: Qian Zhang
'''
#from distutils.core import setup
from bottle import route, run, error
from bottle import get, post, request, response, template
from datetime import datetime

import os, time
import array
import requests
import json
requests.__version__
'1.1.0'
import mysql.connector
from mysql.connector import errorcode

os.environ['TZ'] = 'America/New_York'


@get('/getTable')
def getParameters():
    
    try:
        cnx = mysql.connector.connect(user='zhan1803', password='surf2016vaccine', host='pixel.ecn.purdue.edu', port = 4444, database='VALET')
        cursor = cnx.cursor()
        
        #fetchtable.php
        # Given time range and offense type
        
        time = str(datetime.now())
        print (time)
        
        
        queryFetchTable = ("SELECT DISTINCT lwchrgid, inci_id, offcr_id FROM TESTING_V5 WHERE date_occu>=%s AND date_occu<=%s AND Category='%s'")
        sTime = request.query.startTime
        eTime = request.query.endTime
        category = request.query.offense
        
        cursor.execute(queryFetchTable % (sTime, eTime, category))
        resultSet = cursor.fetchall()
        
        if resultSet is not None:
#             for row in resultSet:
#                 print ('%s %s %s' % (row[0], row[1], row[2]))
            
            print("success!")   # test
        
            cursor.close()
            cnx.close()

            return calcCount(resultSet)
        
        else:
            print("empty!")   # test
            cursor.close()
            cnx.close()
            return
        
    except mysql.connector.Error as err:
        if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
            print("Something is wrong with your user name or password")
        elif err.errno == errorcode.ER_BAD_DB_ERROR:
            print("Database does not exist")
        else:
            print(err)
    else:
        cursor.close()
        cnx.close()
     
    print("failure!")    #test   
    cursor.close()
    cnx.close()   
    return 


    
@get('/calcRank')
def calcCount(resultSet):

    try:
        cnx = mysql.connector.connect(user='zhan1803', password='surf2016vaccine', host='pixel.ecn.purdue.edu', port = 4444, database='ZhanTempDB')
        cursor = cnx.cursor()

        queryCreateTable = ("""CREATE TABLE IF NOT EXISTS officer_count
        (
        id int UNIQUE AUTO_INCREMENT,
        offcr_id smallint primary key NOT NULL,
        count int NOT NULL)""")
        
        cursor.execute(queryCreateTable)
        
        if resultSet is not None:
            for row in resultSet:
                officerId = row[2]
#                 print ('%s %s %s' % (row[0], row[1], row[2]))
                
                queryInsert = ("INSERT INTO officer_count (offcr_id, count) VALUES (%s, %s) ON DUPLICATE KEY UPDATE count=count+1")
                cursor.execute(queryInsert % (officerId, 2))
                
            querySelect = ("SELECT offcr_id, count FROM officer_count ORDER BY count DESC") 
            cursor.execute(querySelect)
            selectSet = cursor.fetchall()
            
            if selectSet is not None:
                for row in selectSet:
                    print ('%s %s' % (row[0], row[1]))
                
                print ("Selected %s rows" % cursor.rowcount)   
                json_str = json.dumps(selectSet)
                
                dropTable = ("DROP TABLE IF EXISTS officer_count")
                cursor.execute(dropTable)
                
                time = str(datetime.now())
                print (time)
                
                cursor.close()
                cnx.close()
                return json_str 
            
            else:
                print("empty!")   # test
                cursor.close()
                cnx.close()
                return
         
        else:
            print("empty!")   # test
            cursor.close()
            cnx.close()
            return
               
        cursor.close()
        cnx.close()
        
        return
         
    except mysql.connector.Error as err:
        if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
            print("Something is wrong with your user name or password")
        elif err.errno == errorcode.ER_BAD_DB_ERROR:
            print("Database does not exist")
        else:
            print(err)
    else:
        cursor.close()
        cnx.close()
    
    cursor.close()
    cnx.close()     
    return 





@get('/getTable1')
def getParameters1():
    
    try:
        cnx = mysql.connector.connect(user='zhan1803', password='surf2016vaccine', host='pixel.ecn.purdue.edu', port = 4444, database='VALET')
        cursor = cnx.cursor()
        
        time = str(datetime.now())
        print (time)
        
        
        #fetchtable.php
        # Given time range and offense type
        queryFetchTable = ("SELECT DISTINCT offcr_id FROM TESTING_V5 WHERE date_occu>=%s AND date_occu<=%s AND Category='%s'")
        sTime = request.query.startTime
        eTime = request.query.endTime
        category = request.query.offense
        
        cursor.execute(queryFetchTable % (sTime, eTime, category))
        resultSet = cursor.fetchall()
        
        if resultSet is not None:
            for row in resultSet:
                officer_id = row[0]
                queryCount = ("SELECT count(*) FROM TESTING_V5 WHERE offcr_id=%s AND date_occu>=%s AND date_occu<=%s AND Category='%s' GROUP BY Category")
#                 queryCount = ("SELECT Category, count(*) FROM TESTING_V5 WHERE offcr_id=375 AND date_occu>=20150615 AND date_occu<=20160615 AND callsource <> 'SELF' GROUP BY Category")
#                 cursor.execute(queryCount)
                cursor.execute(queryCount % (officer_id, sTime, eTime, category))
#                 print ('%s %s %s' % (row[0], row[1], row[2]))
                selectSet = cursor.fetchone()
                print ('%s %s' % (officer_id, selectSet[0]))
             
            time = str(datetime.now())
            print (time)
             
            print("success!")   # test
        
            cursor.close()
            cnx.close()

            return 
        
        else:
            print("empty!")   # test
            cursor.close()
            cnx.close()
            return
        
    except mysql.connector.Error as err:
        if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
            print("Something is wrong with your user name or password")
        elif err.errno == errorcode.ER_BAD_DB_ERROR:
            print("Database does not exist")
        else:
            print(err)
    else:
        cursor.close()
        cnx.close()
     
    print("failure!")    #test   
    cursor.close()
    cnx.close()   
    return 

########################################################################
# By: Qian Zhang
#
# Function Description:
# Given time period
# Given offense type
# Rank counts of the particular offense for all officers from highest to lowest
########################################################################

@get('/getofficerrank')
def getOffierRank():
    
    try:
        cnx = mysql.connector.connect(user='zhan1803', password='surf2016vaccine', host='pixel.ecn.purdue.edu', port = 4444, database='VALET')
        cursor1 = cnx.cursor()
        cursor2 = cnx.cursor()
        
        time = str(datetime.now())
        print (time)
        
        sTime = request.query.startTime
        eTime = request.query.endTime
        category = request.query.offense
        
        if category == "Null":
            queryDistId = ("SELECT DISTINCT offcr_id FROM TESTING_V5 WHERE date_occu>=%s AND date_occu<=%s AND Category IS NULL")
            queryId = ("SELECT offcr_id FROM TESTING_V5 WHERE date_occu>=%s AND date_occu<=%s AND Category IS NULL")
            
            cursor1.execute(queryDistId % (sTime, eTime))
            distinctSet = cursor1.fetchall()
            
            cursor2.execute(queryId % (sTime, eTime))
            nondistinctSet = cursor2.fetchall()
            
        else:
            queryDistId = ("SELECT DISTINCT offcr_id FROM TESTING_V5 WHERE date_occu>=%s AND date_occu<=%s AND Category='%s'")
            queryId = ("SELECT offcr_id FROM TESTING_V5 WHERE date_occu>=%s AND date_occu<=%s AND Category='%s'")
            
            cursor1.execute(queryDistId % (sTime, eTime, category))
            distinctSet = cursor1.fetchall()
            
            cursor2.execute(queryId % (sTime, eTime, category))
            nondistinctSet = cursor2.fetchall()
        
        offcr = array.array("i")
        count = array.array("i")

        if distinctSet is not None:
            for row in distinctSet:
                officer_id = row[0]
                offcr.append(officer_id)
                count.append(0)
                
            for index in range(len(nondistinctSet)):
                officer_id = nondistinctSet[index]
                for i, j in enumerate(distinctSet):
                    if j == officer_id:
                        count[i] += 1
                        break
            
            officer_dictionary=dict()

            for index in range(len(distinctSet)):
                officer_dictionary[str(offcr[index])] = count[index]
            
            sorted_dic_key = sorted(officer_dictionary, key = officer_dictionary.__getitem__, reverse=True)
                  
            officerMatrix = [[0 for x in range(3)] for y in range(len(sorted_dic_key))] 
            
            for i in range(len(sorted_dic_key)):
                officerMatrix[i][0] = i+1
                officerMatrix[i][1] = sorted_dic_key[i]
                officerMatrix[i][2] = officer_dictionary[sorted_dic_key[i]]

# Correct!!!
#             for i in sorted_dic_key:
#                 print('%s %s' % (i, officer_dictionary[i]))
                
            for i in range(len(sorted_dic_key)):
                print('%s %s %s' % (officerMatrix[i][0], officerMatrix[i][1], officerMatrix[i][2]))
            
            json_str = json.dumps(officerMatrix)
            sorted_dic_key.clear()
            officerMatrix.clear()
           
            print("success!")   # test
            
            time1 = str(datetime.now())
            print (time)
            print (time1)
            
            cursor1.close()
            cursor2.close()
            cnx.close()

            return json_str
        
        else:
            print("empty!")   # test
            cursor1.close()
            cursor2.close()
            cnx.close()
            return
        
    except mysql.connector.Error as err:
        if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
            print("Something is wrong with your user name or password")
        elif err.errno == errorcode.ER_BAD_DB_ERROR:
            print("Database does not exist")
        else:
            print(err)
    else:
        cursor1.close()
        cursor2.close()
        cnx.close()
     
    print("failure!")    #test   
    cursor1.close()
    cursor2.close()
    cnx.close()   
    return 


########################
# By: Qian Zhang
#
# Function Description:
# Given time period
# Calculate counts grouped by Category for all officers
# Within one officer, rank counts descending
########################

@get('/getoffensecount')
def getOffenseCount():
    try:        
        time = str(datetime.now())
        print (time)
        
        cnx = mysql.connector.connect(user='zhan1803', password='surf2016vaccine', host='pixel.ecn.purdue.edu', port = 4444, database='VALET')
        cursor = cnx.cursor()
        
        sTime = request.query.startTime
        eTime = request.query.endTime
        
        queryDistId = ("SELECT DISTINCT offcr_id FROM TESTING_V5 WHERE date_occu>=%s AND date_occu<=%s GROUP BY offcr_id")
        cursor.execute(queryDistId % (sTime, eTime))
        distinctIdSet = cursor.fetchall()
        
        queryDistIdCate = ("SELECT offcr_id, Category FROM TESTING_V5 WHERE date_occu>=%s AND date_occu<=%s GROUP BY offcr_id, Category")
        cursor.execute(queryDistIdCate % (sTime, eTime))
        distinctIdCateSet = cursor.fetchall()
        
        queryNonDistIdCate = ("SELECT offcr_id, Category FROM TESTING_V5 WHERE date_occu>=%s AND date_occu<=%s")
        cursor.execute(queryNonDistIdCate % (sTime, eTime))
        nonDistinctIdCateSet = cursor.fetchall()
        
        
        if distinctIdCateSet is not None:
            offenseCountMatrix = [[0 for x in range(3)] for y in range(len(distinctIdCateSet))] 
            officerRepeatTimes = [0 for x in range(len(distinctIdSet))]
        
            j = 0
            for i in range(len(distinctIdSet)):
                while(j < len(distinctIdCateSet)):
                    if(distinctIdCateSet[j][0] == distinctIdSet[i][0]):
                        officerRepeatTimes[i] += 1
                        j += 1
                    else:
                        break     
                     
            for i in range(len(distinctIdCateSet)):
                offenseCountMatrix[i][0] = distinctIdCateSet[i][0]
                offenseCountMatrix[i][1] = distinctIdCateSet[i][1]
                offenseCountMatrix[i][2] = 0
#                 print('%s %s %s %s' % (i+1, offenseCountMatrix[i][0], offenseCountMatrix[i][1], offenseCountMatrix[i][2]))

            for i in range(len(nonDistinctIdCateSet)):
                offcr_id = nonDistinctIdCateSet[i][0]
                category = nonDistinctIdCateSet[i][1]
                for j in range(len(offenseCountMatrix)):
                    if (offcr_id==offenseCountMatrix[j][0] and category==offenseCountMatrix[j][1]):
                        offenseCountMatrix[j][2] += 1

            k = 0       # index for distinctIdCateSet
            for i in range(len(officerRepeatTimes)):
                tempCategory = []
                tempCount = []
                tempDictionary = dict()
                for j in range(officerRepeatTimes[i]):
                    tempCategory.append(str(offenseCountMatrix[k][1]))
                    tempCount.append(offenseCountMatrix[k][2])
                    tempDictionary[tempCategory[j]] = tempCount[j]
                    k += 1 
                
                tempCategory.clear()
                tempCount.clear()
                       
                sortTempMatrix = sorted(tempDictionary, key = tempDictionary.__getitem__, reverse=True)
                index = k - officerRepeatTimes[i]
                 
                for j in range(len(sortTempMatrix)):
                    offenseCountMatrix[index][1] = sortTempMatrix[j]
                    offenseCountMatrix[index][2] = tempDictionary[sortTempMatrix[j]]
                    print('%s %s %s' % (offenseCountMatrix[index][0], offenseCountMatrix[index][1], offenseCountMatrix[index][2]))
                    index += 1
                print('#####################')   
                tempDictionary.clear()
        
            json_str = json.dumps(offenseCountMatrix)
            offenseCountMatrix.clear()
            
            time1 = str(datetime.now())
            print (time)
            print (time1)
            
            cursor.close()
            cnx.close() 
            return json_str
              
        else:
            print("empty!")   # test
            cursor.close()
            cnx.close()      
            return
         
    except mysql.connector.Error as err:
        if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
            print("Something is wrong with your user name or password")
        elif err.errno == errorcode.ER_BAD_DB_ERROR:
            print("Database does not exist")
        else:
            print(err)
    else:
        cursor.close()
        cnx.close()   
        return
        
    cursor.close()
    cnx.close()      
    return


########################################################################
# By: Qian Zhang
#
# Function Description:
# Select Distinct Category from database
# Returned category is sorted alphabetically
########################################################################


@get('/getoffenselist')
def getOffenseList():
    try:
        cnx = mysql.connector.connect(user='zhan1803', password='surf2016vaccine', host='pixel.ecn.purdue.edu', port = 4444, database='VALET')
        cursor = cnx.cursor()
        
        queryOffenseList = ("SELECT Category FROM TESTING_V5 GROUP BY Category")
        cursor.execute(queryOffenseList)
        offenseList = cursor.fetchall()
        
        json_str = json.dumps(offenseList)        
        
        cursor.close()
        cnx.close() 
        return json_str
        
    except mysql.connector.Error as err:
        if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
            print("Something is wrong with your user name or password")
        elif err.errno == errorcode.ER_BAD_DB_ERROR:
            print("Database does not exist")
        else:
            print(err)
    else:
        cursor.close()
        cnx.close()   
        return
        
    cursor.close()
    cnx.close()      
    return 












#emdept_id

@get('/getofficerranknew')
def getOffierRankNew():
    
    try:
        cnx = mysql.connector.connect(user='zhan1803', password='surf2016vaccine', host='pixel.ecn.purdue.edu', port = 4444, database='VALET')
        cursor1 = cnx.cursor()
        cursor2 = cnx.cursor()
        
        time = str(datetime.now())
        print (time)
        
        sTime = request.query.startTime
        eTime = request.query.endTime
        category = request.query.offense
        
        if category == "Null":
            queryDistId = ("SELECT DISTINCT emdept_id FROM TESTING_V6 WHERE date_occu>=%s AND date_occu<=%s AND Category IS NULL")
            queryId = ("SELECT emdept_id FROM TESTING_V6 WHERE date_occu>=%s AND date_occu<=%s AND Category IS NULL")
            
            cursor1.execute(queryDistId % (sTime, eTime))
            distinctSet = cursor1.fetchall()
            
            cursor2.execute(queryId % (sTime, eTime))
            nondistinctSet = cursor2.fetchall()
            
        else:
            queryDistId = ("SELECT DISTINCT emdept_id FROM TESTING_V6 WHERE date_occu>=%s AND date_occu<=%s AND Category='%s'")
            queryId = ("SELECT emdept_id FROM TESTING_V6 WHERE date_occu>=%s AND date_occu<=%s AND Category='%s'")
            
            cursor1.execute(queryDistId % (sTime, eTime, category))
            distinctSet = cursor1.fetchall()
            
            cursor2.execute(queryId % (sTime, eTime, category))
            nondistinctSet = cursor2.fetchall()
        
        offcr = []
        count = array.array("i")

        if distinctSet is not None:
            for row in distinctSet:
                officer_id = row[0]
                offcr.append(officer_id)
                count.append(0)
                
            for index in range(len(nondistinctSet)):
                officer_id = nondistinctSet[index]
                for i, j in enumerate(distinctSet):
                    if j == officer_id:
                        count[i] += 1
                        break
            
            officer_dictionary=dict()

            for index in range(len(distinctSet)):
                officer_dictionary[str(offcr[index])] = count[index]
            
            sorted_dic_key = sorted(officer_dictionary, key = officer_dictionary.__getitem__, reverse=True)
                  
            officerMatrix = [[0 for x in range(3)] for y in range(len(sorted_dic_key))] 
            
            for i in range(len(sorted_dic_key)):
                officerMatrix[i][0] = i+1
                officerMatrix[i][1] = sorted_dic_key[i]
                officerMatrix[i][2] = officer_dictionary[sorted_dic_key[i]]

# Correct!!!
#             for i in sorted_dic_key:
#                 print('%s %s' % (i, officer_dictionary[i]))
                
            for i in range(len(sorted_dic_key)):
                print('%s %s %s' % (officerMatrix[i][0], officerMatrix[i][1], officerMatrix[i][2]))
            
            json_str = json.dumps(officerMatrix)
            sorted_dic_key.clear()
            officerMatrix.clear()
           
            print("success!")   # test
            
            time1 = str(datetime.now())
            print (time)
            print (time1)
            
            cursor1.close()
            cursor2.close()
            cnx.close()

            return json_str
        
        else:
            print("empty!")   # test
            cursor1.close()
            cursor2.close()
            cnx.close()
            return
        
    except mysql.connector.Error as err:
        if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
            print("Something is wrong with your user name or password")
        elif err.errno == errorcode.ER_BAD_DB_ERROR:
            print("Database does not exist")
        else:
            print(err)
    else:
        cursor1.close()
        cursor2.close()
        cnx.close()
     
    print("failure!")    #test   
    cursor1.close()
    cursor2.close()
    cnx.close()   
    return 


########################
# By: Qian Zhang
#
# Function Description:
# Given time period
# Calculate counts grouped by Category for all officers
# Within one officer, rank counts descending
########################


#emdept_id

@get('/getoffensecountnew')
def getOffenseCountNew():
    try:        
        time = str(datetime.now())
        print (time)
        
        cnx = mysql.connector.connect(user='zhan1803', password='surf2016vaccine', host='pixel.ecn.purdue.edu', port = 4444, database='VALET')
        cursor = cnx.cursor()
        
        sTime = request.query.startTime
        eTime = request.query.endTime
        
        queryDistId = ("SELECT DISTINCT emdept_id FROM TESTING_V6 WHERE date_occu>=%s AND date_occu<=%s GROUP BY emdept_id")
        cursor.execute(queryDistId % (sTime, eTime))
        distinctIdSet = cursor.fetchall()
        
        queryDistIdCate = ("SELECT emdept_id, Category FROM TESTING_V6 WHERE date_occu>=%s AND date_occu<=%s GROUP BY emdept_id, Category")
        cursor.execute(queryDistIdCate % (sTime, eTime))
        distinctIdCateSet = cursor.fetchall()
        
        queryNonDistIdCate = ("SELECT emdept_id, Category FROM TESTING_V6 WHERE date_occu>=%s AND date_occu<=%s")
        cursor.execute(queryNonDistIdCate % (sTime, eTime))
        nonDistinctIdCateSet = cursor.fetchall()
        
        
        if distinctIdCateSet is not None:
            offenseCountMatrix = [[0 for x in range(3)] for y in range(len(distinctIdCateSet))] 
            officerRepeatTimes = [0 for x in range(len(distinctIdSet))]
        
            j = 0
            for i in range(len(distinctIdSet)):
                while(j < len(distinctIdCateSet)):
                    if(distinctIdCateSet[j][0] == distinctIdSet[i][0]):
                        officerRepeatTimes[i] += 1
                        j += 1
                    else:
                        break     
                     
            for i in range(len(distinctIdCateSet)):
                offenseCountMatrix[i][0] = distinctIdCateSet[i][0]
                offenseCountMatrix[i][1] = distinctIdCateSet[i][1]
                offenseCountMatrix[i][2] = 0
#                 print('%s %s %s %s' % (i+1, offenseCountMatrix[i][0], offenseCountMatrix[i][1], offenseCountMatrix[i][2]))

            for i in range(len(nonDistinctIdCateSet)):
                offcr_id = nonDistinctIdCateSet[i][0]
                category = nonDistinctIdCateSet[i][1]
                for j in range(len(offenseCountMatrix)):
                    if (offcr_id==offenseCountMatrix[j][0] and category==offenseCountMatrix[j][1]):
                        offenseCountMatrix[j][2] += 1

            k = 0       # index for distinctIdCateSet
            for i in range(len(officerRepeatTimes)):
                tempCategory = []
                tempCount = []
                tempDictionary = dict()
                for j in range(officerRepeatTimes[i]):
                    tempCategory.append(str(offenseCountMatrix[k][1]))
                    tempCount.append(offenseCountMatrix[k][2])
                    tempDictionary[tempCategory[j]] = tempCount[j]
                    k += 1 
                
                tempCategory.clear()
                tempCount.clear()
                       
                sortTempMatrix = sorted(tempDictionary, key = tempDictionary.__getitem__, reverse=True)
                index = k - officerRepeatTimes[i]
                 
                for j in range(len(sortTempMatrix)):
                    offenseCountMatrix[index][1] = sortTempMatrix[j]
                    offenseCountMatrix[index][2] = tempDictionary[sortTempMatrix[j]]
                    print('%s %s %s' % (offenseCountMatrix[index][0], offenseCountMatrix[index][1], offenseCountMatrix[index][2]))
                    index += 1
                print('#####################')   
                tempDictionary.clear()
        
            json_str = json.dumps(offenseCountMatrix)
            offenseCountMatrix.clear()
            
            time1 = str(datetime.now())
            print (time)
            print (time1)
            
            cursor.close()
            cnx.close() 
            return json_str
              
        else:
            print("empty!")   # test
            cursor.close()
            cnx.close()      
            return
         
    except mysql.connector.Error as err:
        if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
            print("Something is wrong with your user name or password")
        elif err.errno == errorcode.ER_BAD_DB_ERROR:
            print("Database does not exist")
        else:
            print(err)
    else:
        cursor.close()
        cnx.close()   
        return
        
    cursor.close()
    cnx.close()      
    return

#run(host='localhost', port=8080, debug=True)
run(host='128.46.137.96', port=8080, debug=True)


#####################################################################
#     server = getenv("pixel.ecn.purdue.edu:4444")
#     user = getenv("zhan1803@pixel.ecn.purdue.edu")
#     password = getenv("surf2016vaccine")
#     conn = pymssql.connect(host="pixel.ecn.purdue.edu", user="zhan1803", password="surf2016vaccine", port=4444,database="VALET")
#     conn = pymssql.connect("pixel.ecn.purdue.edu:4444", "zhan1803@pixel.ecn.purdue.edu:4444", password="surf2016vaccine", port=4444,database="VALET")
#     conn = pymssql.connect(server, user, password, "VALET")
#     cursor = conn.cursor()
#     result = cursor.execute('SELECT Category FROM TESTING_V5 WHERE inci_id=%d', 2013011594)
#####################################################################
    

