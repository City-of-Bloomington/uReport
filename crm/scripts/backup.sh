#!/bin/bash
# Creates a tarball containing a full snapshot of the data in the site
#
# @copyright 2011-2014 City of Bloomington, Indiana
# @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
# @author Cliff Ingham <inghamn@bloomington.in.gov>

# Where to store the nightly backup tarballs
BACKUP_DIR=/srv/backups/crm
# The path to the mysqldump executable
MYSQLDUMP=/usr/local/mysql/bin/mysqldump
# Your APPLICATION_HOME from configuration.inc
APPLICATION_HOME=/srv/sites/crm
# You SITE_HOME from configuration.inc
SITE_HOME=$APPLICATION_HOME/data
# Where to store the database dump files
DB_DUMP_DIR=$SITE_HOME/database_backup

MYSQL_DBNAME=crm

# How many days worth of backups to keep around
num_days_to_keep=5

#----------------------------------------------------------
# No editing is usually required below this line
#----------------------------------------------------------
now=`date +%s`
today=`date +%F`

# Dump the database
$MYSQLDUMP --defaults-extra-file=$APPLICATION_HOME/scripts/backup.cnf $MYSQL_DBNAME > $DB_DUMP_DIR/$MYSQL_DBNAME.sql

# Tarball the Data
cd $SITE_HOME/..
basename=${SITE_HOME##*/}
tar czf $BACKUP_DIR/$today.tar.gz $basename

# Purge any backup tarballs that are too old
cd $BACKUP_DIR
for file in `ls`
do
	atime=`stat -c %Y $file`
	if [ $(( $now - $atime >= $num_days_to_keep*24*60*60 )) = 1 ]
	then
		rm $file
	fi
done
