========================================
=   HowTo setup Comanche web project   =
========================================


Preamble
--------

   Comanche intends to gather and format MD topologies in order to extend the number of available lipids 
   and build membranes in an automated way based on a CG2AT flow. More complex lipid compositions will then be available.
   This project will provide a web interface where you can input a molecule (and its topology if needed) 
   and choose a membrane composition to compute their interaction with the Gromacs software on distant nodes. 

Initial setup
-------------

   Install the latest Ubuntu Gnome Desktop on all computers with a common user name and password on all systems. 
   In a first development step Virtualbox will be used.
   comanche will be our common user name on all systems. 
   sudo password should match on all systems.
   MySQL and phpmyadmin passwords will match system root password (master only).
   Make sure all systems have time settings synchronized.


Master configuration
--------------------

   1) Install the following components on Master 

      apache2 		(web directory /var/www/)
      mysql-server 	(create root user and password “comanche”) 
      php5
      php5-gd 		(for image to text engine - captcha) 
      phmypadmin 	(create root user and password )
      munge 		(create authentication key on Master and copy to all nodes)
      slurm-llnl 	(create slurm.conf from web page and copy to all nodes and Master)
      nfs-kernel-server
      nfs-common
      ntp
      build-essential
      cmake
      python-pip
      libmysqlclient-dev
      python-dev
      python-mysqldb
      python3-mysql.connector
      
      pip install mysql
      pip install MySQL-python
      pip install mysql-connector-python --allow-external mysql-connector-python

      if the last command doesn't work:
         cd ~/Software 
         wget https://cdn.mysql.com/Downloads/Connector-Python/mysql-connector-python-2.0.3.zip
         unzip mysql-connector-python-2.0.3.zip
         cd mysql-connector-python-2.0.3
         python setup.py install
      
   2) Configure /etc/hosts (in sudo mode)
	
      -----hosts---start----------
      127.0.0.1		localhost linux1 
      127.0.1.1		comancheM  
      192.168.1.2	comancheN1 
      -----hosts---end------------

   3) Configure /etc/network/interfaces (in sudo mode)

      -----interfaces---start----------
      auto lo 
      iface lo inet loopback 
      
      # slurm network 
      auto eth1 
      iface eth1 inet static 
      address 192.168.1.1 
      netmask 255.255.255.0
      -----interfaces---end------------
      
   4) Configure the nfs

      Add the following line to /etc/export
         /home/ 192.168.1.2(rw,all_squash,anonuid=1000,anongid=1000,sync)
      and start the service 
         /etc/init.d/nfs-kernel-server start

   5) Configure the ntp

      Remplace the default servers in /etc/ntp.conf by
         server 0.fr.pool.ntp.org iburst dynamic
         server 1.fr.pool.ntp.org iburst dynamic
         server 2.fr.pool.ntp.org iburst dynamic
         server 3.fr.pool.ntp.org iburst dynamic
      and start the service 
         sudo /etc/init.d/ntp restart
      
   6) Test apache works and copy the comanche web site
	
      In firefox goto http://localhost/index.html and you should see message “It works”
      now delete index.html page
 
      Replace /var/www/html by the comanche www directory 
      
      In firefox goto: http://localhost/ you should see comanche login page
      
   7) Configure sql (in sudo mode)

      Edit /etc/mysql/my.cnf and change the following line
         bind-address		= 192.168.1.1
      
      Launch phpmyadmin by going to http://localhost/myphpadmin/
      Import the comanche_db.sql file 
      
      In /etc/apache2/conf.d/phpmyadmin.conf edit line "Allow from 127.0.0.1/24" to "Allow from 192.168.1.1/24"
      This change will limit access via “eth1” and will not be visible on internet
      
   8) Create /home/comanche/Job_run.sh:
      
      This script will be launch by the web interface and has for job to submit the computation script comanche.sh to slurm
      
   9) Setup Munge
      
      create a munge key and copy it to /etc/munge/munge.key on master and all nodes
      
         #/usr/sbin/create-munge-key

      Add the option –-force in /etc/default/munge

         #/etc/init.d/munge start
      
   10) Setup slurm 
      
      run the provided configurator and copy slurm.conf in /etc/slurm-llnl
      In firefox goto : file:///usr/share/doc/slurm-llnl/slurm-llnl-configurator.html
      fill in the form and copy text into /etc/slurm-llnl/slurm.conf
      
      or copy the provided slurm.conf
      
      Test slurm with commands like
      
         #/etc/init.d/slurm-llnl start
         #scontrol show daemons
         #sinfo
      
   11) Secureimage download and steup
      
      Copy secureimage directory to /var/www/
      http://www.phpcaptcha.org/documentation/quickstart-guide/

   12) Install gromacs
      
      bash instal_gmx507.bash
      
      Add the following alias in .bashrc
         alias gmx507="source /home/comanche/Software/gmx507/build/bin/GMXRC && export GMXLIB='/home/comanche/Software/gmx507/build/share/gromacs/top'"
         alias gmx467="source /home/comanche/Software/gmx467/build/bin/GMXRC && export GMXLIB='/home/comanche/Software/gmx467/build/share/gromacs/top'"
       
      The 4.6.7 verison need to be installed now for the reverse step 


Node configuration
------------------
      
   1) Installed components on Nodes:
      
      libnet-daemon-perl
      libplrpc-perl
      libdbi-perl
      libdbd-mysql-perl
      mysql-client-core-5.6
      mysql-client-5.6
      munge 		(must copy munge key from master to /etc/munge/ dir)
      slurm-llnl 	(must copy slurm.conf from master to /etc/slurm-llnl/ dir )
      build-essential
      cmake
      nfs-common
      ntp
      
   2) configure /etc/hosts: (in sudo mode)
      
      -----hosts---start----------
      172.0.0.1		localhost
      192.168.1.1	comancheM
      192.168.1.2	comancheN1
      192.168.1.3	comancheN2  
      192.168.1.4	comancheN3  
      -----hosts---end------------
      
      Note: slurm uses host file to resolve node IP address
      
   3) configure /etc/network/interfaces (in sudo mode)
      
      -----interface---start----------
      auto lo 
      iface lo inet loopback 
      
      # slurm network 
      auto eth0 
      iface eth0 inet static
      address	192.168.1.2
      netmask	255.255.255.0
      -----interface---end------------
      
   4) Modified /etc/fstab to mount home at boot time: (in sudo mode)
      	
      192.168.1.1:/home /home nfs4 rw,noatime 0 2 
      
   5) Configure ntp
      
      remplace default serveurs in /etc/ntp.conf by
         server 0.fr.pool.ntp.org iburst dynamic
         server 1.fr.pool.ntp.org iburst dynamic
         server 2.fr.pool.ntp.org iburst dynamic
         server 3.fr.pool.ntp.org iburst dynamic
      and restart the service 
         sudo /etc/init.d/ntp restart
      
   6) Created remote script comanche.sh on all nodes:
      
      copy the script to /home/comanche/
      
      This script is the main script of the comanche project 
      
   7) Install gromacs versions 4.6.7 and 5.0.7

      bash instal_gmx507.bash
      
      Add the following alias in .bashrc
         alias gmx507="source /home/comanche/Software/gmx507/build/bin/GMXRC && export GMXLIB='/home/comanche/Software/gmx507/build/share/gromacs/top'"
         alias gmx467="source /home/comanche/Software/gmx467/build/bin/GMXRC && export GMXLIB='/home/comanche/Software/gmx467/build/share/gromacs/top'"
       
      The 4.6.7 verison need to be installed now for the reverse step 
      
