#!/usr/bin/python

import sys

AA = { "H" : "his", "D" : "asp", "R" : "arg", "F" : "phe", "A" : "ala", "C" : "cys", "G" : "gly", "Q" : "gln", "E" : "glu", "K" : "lys", "L" : "leu", "M" : "met", "N" : "asn", "S" : "ser", "Y" : "tyr", "T" : "thr", "I" : "ile", "W" : "trp", "P" : "pro", "V" : "val" }

lines = open(sys.argv[1]).readlines() 
name = lines[0][1:].strip()
seq = ""
for line in lines[1:]:
   seq = seq + line.strip()
lenght = len(seq)

rib = open(name+".rib","w")
rib.write("title "+name+"\n")
rib.write("default helix\n\n")
for res in seq:
   rib.write("res "+AA[res]+"\n\n")

