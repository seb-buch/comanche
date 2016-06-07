#!/bin/bash

#export TERM=xterm
#export SHELL=/bin/bash
#export USER=peplook
#export DEFAULTS_PATH=/usr/share/gconf/gnome.default.path
#export USERNAME=peplook
##export DESKTOP_SESSION=gnome
#export PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games
#export PWD=/home/peplook
#export GDM_KEYBOARD_LAYOUT=be
#export LANG=en_US.UTF-8
#export MANDATORY_PATH=/usr/share/gconf/gnome.mandatory.path
##export GDM_LANG=en_US.UTF-8
##export GDMSESSION=gnome
##export SPEECHD_PORT=7560
#export HOME=/home/peplook
#export LOGNAME=peplook
##
#export DISPLAY=:0.0

#s="1Z.sh"
#srun ./$s -nodesktop -nosplash -nodisplay

#gmx507
#cd gmxtest/gmx507/1n
#md_0_1.tpr
#cd
srun sleep 30s


V=`date`
echo $V >> Test.txt



