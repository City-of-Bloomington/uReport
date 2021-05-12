#!/bin/bash
# Creates a tarball containing a full snapshot of the data in the site
#
# @copyright 2011-2021 City of Bloomington, Indiana
# @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE

# Where to store the nightly backup tarballs
APPLICATION_NAME="ureport"
MYSQL_DBNAME="{{ ureport_db.name }}"
MYSQL_CREDENTIALS="/etc/cron.daily/backup.d/${APPLICATION_NAME}.cnf"
BACKUP_DIR="{{ ureport_backup_path }}"
APPLICATION_HOME="{{ ureport_install_path }}"
SITE_HOME="{{ ureport_site_home }}"

# How many days worth of backups to keep around
num_days_to_keep=5

#----------------------------------------------------------
# No editing is usually required below this line
#----------------------------------------------------------
now=`date +%s`
today=`date +%F`

# Dump the database
cd $BACKUP_DIR
mysqldump --defaults-extra-file=$MYSQL_CREDENTIALS $MYSQL_DBNAME > $today.sql
gzip $today.sql

# Purge any backup tarballs that are too old
for file in `ls`
do
	atime=`stat -c %Y $file`
	if [ $(( $now - $atime >= $num_days_to_keep*24*60*60 )) = 1 ]
	then
		rm $file
	fi
done
