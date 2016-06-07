#!/usr/bin/python

import urllib
import subprocess
import os.path
import mysql.connector 
import glob
#from warnings import filterwarnings
#import MySQLdb

#filterwarnings('ignore', category = MySQLdb.Warning)

LMDB = [ "FA", "GL", "GP", "SP", "ST", "PR", "SL", "PK" ]  

#comanche_db = mysql.connector.connect(host="localhost",user="root",password="comanche", database="comanche_db")
comanche_db = mysql.connector.connect(host="localhost",user="root",password="comanche")
cursor = comanche_db.cursor()
cursor.execute("CREATE DATABASE IF NOT EXISTS comanche_db")
cursor.execute("use comanche_db")
cursor.execute("""
CREATE TABLE IF NOT EXISTS lipids (
    id int(5) NOT NULL AUTO_INCREMENT,
    lmid varchar(50) DEFAULT NULL,
    commonname varchar(200) DEFAULT NULL,
    systematicname varchar(200) DEFAULT NULL,
    formula varchar(50) DEFAULT NULL,
    mass varchar(50) DEFAULT NULL,
    charge INTEGER DEFAULT NULL,
    catergory varchar(200) DEFAULT NULL, 
    mainclass varchar(200) DEFAULT NULL, 
    subclass varchar(200) DEFAULT NULL, 
    synonyms varchar(200) DEFAULT NULL, 
    keggid varchar(50) DEFAULT NULL, 
    hmdbid varchar(50) DEFAULT NULL, 
    ymdbid varchar(50) DEFAULT NULL, 
    chebiid varchar(50) DEFAULT NULL, 
    metabolomicsid varchar(50) DEFAULT NULL, 
    pubchemcid varchar(50) DEFAULT NULL, 
    lipidbankid varchar(50) DEFAULT NULL, 
    lipidatid varchar(50) DEFAULT NULL, 
    status varchar(50) DEFAULT NULL, 
    refs varchar(200) DEFAULT NULL, 
    lipidmapsurl varchar(200) DEFAULT NULL, 
    PRIMARY KEY(id)
);
""")
cursor.execute("""
CREATE TABLE IF NOT EXISTS forcefield (
    id int(5) NOT NULL AUTO_INCREMENT,
    forcefield varchar(50) DEFAULT NULL,
    lmid varchar(50) DEFAULT NULL,
    common varchar(50) DEFAULT NULL,
    name varchar(50) DEFAULT NULL,
    reverse BOOLEAN DEFAULT NULL,
    status varchar(50) DEFAULT NULL, 
    refs varchar(200) DEFAULT NULL, 
    PRIMARY KEY(id)
);
""")
cursor.execute("""
CREATE TABLE IF NOT EXISTS membranes (
    id int(5) NOT NULL AUTO_INCREMENT,
    membrane varchar(50) DEFAULT NULL,
    forcefield varchar(50) DEFAULT NULL,
    temperature varchar(50) DEFAULT NULL,
    nblipids varchar(50) DEFAULT NULL,
    PRIMARY KEY(id)
);
""")
cursor.execute("""
CREATE TABLE IF NOT EXISTS membranelipids (
    id int(5) NOT NULL AUTO_INCREMENT,
    membrane varchar(50) DEFAULT NULL,
    name varchar(50) DEFAULT NULL,
    number INTEGER DEFAULT NULL,
    position varchar(50) DEFAULT NULL,
    nblipids varchar(50) DEFAULT NULL,
    PRIMARY KEY(id)
);
""")
#for i in range(1):
#   infile = open(LMDB[i]).readlines()
#   for line in infile[1:]:
#       if line.split("\t")[3].strip() != "-":
#           LMID = line.split("\t")[0]
#           print LMID
#           LMC = LMID[0:4]
#           FIELDS = { "LM_ID":"", "COMMON_NAME":"", "SYSTEMATIC_NAME":"", "FORMULA":"", "MASS":"", "CATEGORY":"", "MAIN_CLASS":"", \
#                      "SUB_CLASS":"", "SYNONYMS":"", "KEGG_ID":"", "HMDB_ID":"", "YMDB_ID":"", "CHEBI_ID":"", "METABOLOMICS_ID":"", \
#                      "PUBCHEM_COMPOUND_ID":"", "LIPIDBANK_ID":"", "LIPIDAT_ID":"", "STATUS":"", "REFS":"", "LIPID_MAPS_URL":"" }
#           key = ""
#           for line2 in open("LMSD/"+LMC+"/"+LMID+"/"+LMID+".sdf").readlines():           
#              if line2[0:1] == ">":
#                  key = line2.strip()[3:-1]
#              elif key != "" and key in FIELDS.keys() and line2.strip() != "$$$$":
#                  FIELDS[key] += line2.strip() + " "
#           for key in FIELDS.keys(): 
#              FIELDS[key] = FIELDS[key].strip() 
#           cursor.execute("SELECT lmid FROM lipids WHERE lmid = %s", (LMID, ))
#           rows = cursor.fetchall()
#           if len(rows) == 0:           
#               cursor.execute("INSERT INTO lipids (id, lmid, commonname, systematicname, formula, mass, catergory, mainclass, subclass, synonyms, keggid, hmdbid, ymdbid, chebiid, metabolomicsid, pubchemcid, lipidbankid, lipidatid, status, refs, lipidmapsurl) VALUES(NULL, %(LM_ID)s, %(COMMON_NAME)s, %(SYSTEMATIC_NAME)s, %(FORMULA)s, %(MASS)s, %(CATEGORY)s, %(MAIN_CLASS)s, %(SUB_CLASS)s, %(SYNONYMS)s, %(KEGG_ID)s, %(HMDB_ID)s, %(YMDB_ID)s, %(CHEBI_ID)s, %(METABOLOMICS_ID)s, %(PUBCHEM_COMPOUND_ID)s, %(LIPIDBANK_ID)s, %(LIPIDAT_ID)s, %(STATUS)s, %(REFS)s, %(LIPID_MAPS_URL)s)", FIELDS)
#cursor.execute("TRUNCATE TABLE forcefield")
#for i in glob.glob("../Topologies/*/*/description"):
#   basename = os.path.basename(i) 
#   dirname = os.path.dirname(i)
#   lipid = open(dirname+"/description").readlines()[12].split()[1]
#   forcefield = i.split("/")[-3]
#   lmid = open(dirname+"/description").readlines()[2].split()[1]
#   common = open(dirname+"/description").readlines()[3].split()[1]
#   if os.path.exists(dirname+lipid+".map"):
#       reverse = "TRUE"
#   else:
#       reverse = "FALSE"
#   status = "validated"
#   refs = ""
#   cursor.execute("SELECT lmid FROM forcefield WHERE lmid = %s and forcefield = %s", (lmid, forcefield))
#   rows = cursor.fetchall()
#   if len(rows) == 0:           
#       data = (forcefield, lmid, common, lipid, reverse, status, refs)
#       cursor.execute("INSERT INTO forcefield (id, forcefield, lmid, common, name, reverse, status, refs) VALUES(NULL, %s, %s, %s, %s, %s, %s, %s)", data)
cursor.execute("TRUNCATE TABLE membranes")
cursor.execute("TRUNCATE TABLE membranelipids")
for i in glob.glob("../Membranes/*/*/*/*/topol.top"):
   nblipids = i.split("/")[-2]
   temperature = i.split("/")[-3]
   membrane = i.split("/")[-4]
   forcefield = i.split("/")[-5]
   data = (membrane, forcefield, temperature, nblipids)
   cursor.execute("INSERT INTO membranes (id, membrane, forcefield, temperature, nblipids) VALUES(NULL, %s, %s, %s, %s)", data)
   cursor.execute("SELECT membrane FROM membranelipids WHERE membrane = %s and nblipids = %s", (membrane,nblipids,))
   rows = cursor.fetchall()
   if len(rows) == 0:           
       infile = open(i).readlines()
       lipid = "FALSE"
       position = "upper"
       upper = {}
       lower = {}
       #n = 0
       for line in infile:
           arr = line.split()
           if line[0:1] != ";":
               #n += 1
               #if n == 1:
               #    forcefield = line.split()[1].strip()
               if lipid == "TRUE":
                   name = line.split()[0]
                   number = line.split()[1]

                   #cursor.execute("SELECT common FROM forcefield WHERE name = %s", (name,))
                   #rows = cursor.fetchall()
                   #if len(rows) >= 1:
                   #    print rows[0][0]

                   data = (membrane, name, number, position, nblipids)
                   cursor.execute("INSERT INTO membranelipids (id, membrane, name, number, position, nblipids) VALUES(NULL, %s, %s, %s, %s, %s)", data)
               if len(arr) >= 2: 
                   if line.split()[1] == "molecules":
                       lipid = "TRUE"
           elif len(arr) >= 2:
               if line.split()[1] == "lower":
                   position = "lower"

comanche_db.commit()
comanche_db.close()

#cursor.execute("SELECT id, name, age FROM visiteurs WHERE id = %s", ("1", ))
#rows = cursor.fetchall()
#for row in rows:
#    print('{0} : {1} - {2}'.format(row[0], row[1], row[2]))


