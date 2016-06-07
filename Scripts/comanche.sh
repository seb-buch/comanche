#!/bin/bash

# TODO 
# TODO 

VERSION="1.0" 
AUTHOR="Jean-Marc Crowet, PhD" 
DATE="28-04-2015" 
AFFILIATION="Gembloux Agro-Bio Tech - University of Liège" 

DESCRIPTION=$(cat << __DESCRIPTION__
This is a convenience script to 
__DESCRIPTION__
)

#DEPENDENCIES
#   dssp in /usr/local/bin/dssp
#   gromacs installed in ~/Software with adapted forcefields in share/gromacs/top

source /home/novatux/Software/gmx502/exec2/bin/GMXRC 
export GMXLIB='/home/novatux/Software/gmx502/exec2/share/gromacs/top'


#VARIABLES

START=$(date +%s)
D=9
DZ=9

#OPTIONS

OPTIONS=$(cat << __OPTIONS__

## OPTIONS ##

  script options:
    -f       Input GRO or PDB file 1: Protein                             *FILE:  None 
    -l       Lipid type                                                   *STR:    
    -lp      Lipid relative abundance                                     *STR:    
    -u       Lipid type                                                   *STR:    
    -up      Lipid relative abundance                                     *STR:    
    -salt    Salt concentration                                           *FLOAT:   
    -ff      Atomistic forcefield                                         *STR:    
    -t       Temperature                                                  *INT:    
__OPTIONS__
)

USAGE ()
{
    cat << __USAGE__

$PROGRAM version $VERSION:

$DESCRIPTION

$OPTIONS

(c)$YEAR $AUTHOR
$AFFILIATION

__USAGE__
}


BAD_OPTION ()
{
  echo
  echo "Unknown option "$1" found on command-line"
  echo "It may be a good idea to read the usage:"
  echo -e $USAGE

  exit 1
}

while [ -n "$1" ]; do
  case $1 in
        -h)                   USAGE      ; exit 0 ;;
    # File options
        -f)                   INP=$2      ; shift 2; continue ;;
        -l)      LIPL[${#MDP[@]}]=$2      ; shift 2; continue ;;
       -lp)     LIPLP[${#MDP[@]}]=$2      ; shift 2; continue ;;
        -u)      LIPU[${#MDP[@]}]=$2      ; shift 2; continue ;;
       -up)     LIPUP[${#MDP[@]}]=$2      ; shift 2; continue ;;
     -salt)                  SALT=$2      ; shift 2; continue ;;
       -ff)                    FF=$2      ; shift 2; continue ;;
        -t)                  TEMP=$2      ; shift 2; continue ;;
         *)       BAD_OPTION $1;;
  esac
done


cat << __RUNINFO__

$PROGRAM version $VERSION:

(c)$YEAR $AUTHOR
$AFFILIATION

Now executing...

$CMD

__RUNINFO__



###########################################
## STEP 1: GENERATING STARTING STRUCTURE ##
###########################################

#if Protein
#./rib.py Protein.fasta
#./ribosome64 Protein.rib Protein.pdb res.zmat
#./martinize.py -f Protein.pdb -o topol_temp.top -x Protein_cg.pdb -n index.ndx -ss Protein.ssd -ff martini21 -v
#editconf -f Protein_cg.pdb -o Protein_cg_pos.pdb -princ <<EOF
#0
#EOF
#fi

if FF

./insane.py -o box.gro -p topol.top -pbc square -d 9 -z 9 -l POPC:9 -sol W

sed -i "1d" topol.top
sed -i "2i#include \"martini_v2.0_lipids.itp\"" topol.top
sed -i "2i#include \"martini_v2.1.itp\"" topol.top

#if ions
#grompp -f em0.mdp -p topol.top -c box.gro -o ions.tpr 
#genion -s ions.tpr -o ions.gro -p topol.top -pname NA+ -np 2 <<EOF
#15
#EOF
#fi

#make_ndx -f ions.gro <<EOF
#15 | 17
#13 | 14
#q
#EOF

grompp -f em.mdp -p topol.top -c ions.gro -o em.tpr -maxwarn 1
mdrun -v -deffnm em

grompp -f prod.mdp -p topol.top -c em.gro -n index.ndx -o prod.tpr -maxwarn 1
mdrun -v -deffnm prod -nt 8

rm \#*

