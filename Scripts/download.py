#!/usr/bin/python

import urllib
import subprocess
import os.path

# db has been downloaded as exlained in
# http://www.lipidmaps.org/data/structure/programmaticaccess.html
# However the CoreClass=1 return the whole db 
# date 28/01/2016

LMDB = [ "FA", "GL", "GP", "SP", "ST", "PR", "SL", "PK" ]  
for i in LMDB:
   if not os.path.exists("LMSD/LM"+i): 
       bashCommand = "mkdir LMSD/LM"+i
       process = subprocess.Popen(bashCommand.split(), stdout=subprocess.PIPE)
       output = process.communicate()[0]

for i in range(8):  
#   urllib.urlretrieve("http://www.lipidmaps.org/data/structure/LMSDSearch.php?Mode=ProcessTextSearch&OutputMode=File&CoreClass="+str(i), filename=LMDB[i])
   infile = open(LMDB[i]).readlines()
   for line in infile[1:]:
       if line.split("\t")[3].strip() != "-":
           LMID = line.split("\t")[0]
           print LMID
           LMC = LMID[0:4]
           if not os.path.exists("LMSD/"+LMC): 
               bashCommand = "mkdir LMSD/"+LMC
               process = subprocess.Popen(bashCommand.split(), stdout=subprocess.PIPE)
               output = process.communicate()[0]
           if not os.path.exists("LMSD/"+LMC+"/"+LMID): 
               bashCommand = "mkdir LMSD/"+LMC+"/"+LMID
               process = subprocess.Popen(bashCommand.split(), stdout=subprocess.PIPE)
               output = process.communicate()[0]
           Name = "LMSD/"+LMC+"/"+LMID+"/"+LMID 
           while not os.path.exists(Name+".mol"):
               try:
                   urllib.urlretrieve("http://www.lipidmaps.org/data/LMSDRecord.php?Mode=File&LMID="+LMID, filename=Name+".mol")
               except Exception, e:
                   print e
                   continue
           while not os.path.exists(Name+".sdf"):
               try:
                   urllib.urlretrieve("http://www.lipidmaps.org/data/LMSDRecord.php?Mode=File&LMID="+LMID+"&OutputType=SDF", filename=Name+".sdf")
               except Exception, e:
                   print e
                   continue
           while not os.path.exists(Name+".pdb"):
               bashCommand = "molconvert -3:S{fine} pdb "+Name+".mol -o "+Name+".pdb"
               process = subprocess.Popen(bashCommand.split(), stdout=subprocess.PIPE)
               output = process.communicate()[0]
           while not os.path.exists(Name+"H.pdb"):
               outfile = open("h_add.pml","w")
               outfile.write("load "+Name+".pdb\nh_add\nsave "+Name+"H.pdb\nquit")
               outfile.close()
               bashCommand = "pymol -c h_add.pml"
               process = subprocess.Popen(bashCommand.split(), stdout=subprocess.PIPE)
               output = process.communicate()[0]
           while not os.path.exists(Name+".jpg"):
               mol = open(Name+".mol").readlines()
               X = []
               Y = []
               nb = int(mol[3][0:3])
               for j in range(4,nb+4):
                   X.append(float(mol[j].split()[0]))
                   Y.append(float(mol[j].split()[1]))
               R = (max(Y)-min(Y)) / (max(X)-min(X))
               h = (1+0.04/R) * 1000 * (max(Y)-min(Y)) / (max(X)-min(X))
               bashCommand = "molconvert jpeg:w1000h"+str(h)+",Q95,#ffffff "+Name+".mol -o "+Name+".jpg"
               process = subprocess.Popen(bashCommand.split(), stdout=subprocess.PIPE)
               output = process.communicate()[0]

cd /var/www/html/images/LipidMaps
sudo ./cp.bash

# pdb2gmx
# bashCommand = "babel -imol LMSD/"+LMID+".pdb -opdb LMSD/"+LMID+".gro"
# process = subprocess.Popen(bashCommand.split(), stdout=subprocess.PIPE)
# output = process.communicate()[0]

