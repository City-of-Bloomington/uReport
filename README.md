uReport is a CRM web application with an Open311
(GeoReport v2) endpoint that should meet the needs of smaller municipalities
wishing to deploy Open311 and/or a lightweight constituent management tool.
Context-switched feeds (XML, JSON, etc) allow it to easily be integrated into
existing environments.

Questions and Feedback
----------------------
Online documentation is in this project's
[Wiki](https://github.com/City-of-Bloomington/uReport/wiki)

If you find this software acting in unexpected ways, please create an issue
in our [Issue Tracker](https://github.com/City-of-Bloomington/uReport/issues).

If you want to contribute to the project, join us on our
[Google Group](https://groups.google.com/forum/?fromgroups#!forum/ureport)

New in 1.8
----------------
* Support for Solr 4.0
* Nicer looking icons (from FontAwesome)
* People can now have multiple addresses, emails, and phone numbers
* Locations are validated against a city's lat/long bounding box
* Support for weekly digest emails to employees
* Support for tracking Service Level Agreements
* Bugfixes!

Detailed change log is in the issue tracker:
https://github.com/City-of-Bloomington/uReport/issues?milestone=2&state=closed

Updating to 1.8
----------------------
The 1.8 release involves a data migration.  There are numerous changes to the backend MySQL and Solr schemas.  The MySQL database will need to be modified, the new Solr schema put into place, and the Solr index re-indexed.  Code for the migration is in /scripts/migration/1.7-1.8.  The migration code will be continually updated as features are committed.  So, if you've run it once, you might check the scripts for new changes periodically.

### 1. Backup ###
Backup your existing install.  Do a mysqldump of your database, preserve your existing configuration.inc, and preserve your data directory.  If it all goes haywire, you want to be able to restore!

### 2. Download the latest version ###
You can either clone the project from Github, or just download a compressed copy from the tags page:
https://github.com/City-of-Bloomington/uReport/tags

### 3. Replace your existing install ###
If you move your existing configuration.inc and data directory out of the way, you can just delete your existing install directory, replacing it with the new copy you downloaded.  Then, move your configuration.inc and data directory back in place, inside the fresh install.

Double check to make sure Apache still has permission to write files into the data directory.

### 4. Update your configuration.inc ###
There are a few new configuration settings that must be added to your previous configuration.inc.  You can copy the default settings from configuration.inc.default into your previous configuration.inc.  Inside configuration.inc.default, look for, and copy these settings into your previous configuration.inc.
```php
<?php
define('SOLR_PHP_CLIENT', APPLICATION_HOME.'/libraries/solr-php-client');

/**
 * Bounding box for valid locations
 *
 * Comment these out if you do not want this validation.
 * Tickets will be rejected if they have coordinates that
 * do not fall inside the defined bounding box.
 */
//define('MIN_LATITUDE',   39.069187);
//define('MAX_LATITUDE',   39.99915);
//define('MIN_LONGITUDE', -86.641575);
//define('MAX_LONGITUDE', -86.440543);

/**
 * Image handling library
 * Set the path to the ImageMagick binaries
 */
define('IMAGEMAGICK','/usr/bin');
define('THUMBNAIL_SIZE', 150);

define('CLOSING_COMMENT_REQUIRED_LENGTH', 1);
```

### 5. Run the database migration ###
The database migration script is in: `/scripts/migration/1.7-1.8`.

The SQL script contains a bunch of SQL commands to run.  You should be
able to run them all at once just by sending the file to mysql
```bash
mysql -p crm < databaseChanges.sql
```
If you want to be on the safe side, you might open the
databaseChanges.sql file and read through it.  The only thing I can
think that might need to be checked would be on the "tickets" table.
I'm removing a foreign key and just want to make sure the foreign key
that's being removed is for the resolution_id.  Most likely, the code is
fine as written, though.  You can do a "show create table" to look at
the foreign keys and confirm that the foreign key we're removing is the
resolution_id.

```sql
show create table tickets\G
```

You could also copy and paste the commands into a mysql client, if you
prefer.

### 6. Update your Solr schema.xml ###
Once you've applied all the database changes, you'll need to update your
Solr index.  You'll need to replace your existing schema.xml with the
new one.

The `/scripts/migration/1.7-1.8/newSolrSchema.xml` is identical to the `/scripts/solr/schema.xml`.  The file is in the migration directory as a reminder that you need to update your copy.

You'll need to delete the data in your core and restart Tomcat.
Once Tomcat restarts, you should be able to go back to `/scripts/solr`. In there is a php script for re-indexing the Solr search engine. You should be able to run that from the command line on your server.
```bash
cd /crm/scripts/solr
php indexSearch.php
```

For us, with 100,000 records, the search indexing takes about 5 minutes.

### 7. Enjoy ###
That's it, you should be up and running with the 1.8 version.  If you run into trouble, you can probably get help from folks in our Google Group:
https://groups.google.com/forum/?fromgroups#!forum/ureport


New in 1.7.3
----------------
We recently migrated this project from Google Code.  However, we did not migrate all
the past issue tracker history from the Google Code project.

http://civic-crm.googlecode.com

The 1.7.3 release has issues spanning both systems.
Previous releases were done in the Google Code project.  Issue tracker history
for those fixes are still saved in the old, Google Code project.

* http://code.google.com/p/civic-crm/issues/list?can=7
* https://github.com/City-of-Bloomington/uReport/issues?milestone=1&page=1&state=closed

New in 1.7.2
------------
* Fancy new Activity Report
* Minor bug fixes

https://code.google.com/p/civic-crm/issues/list?can=1&q=Milestone%3D1.7.2

New in 1.7.1
------------
* Fixes for bugs introduced in 1.7.

https://code.google.com/p/civic-crm/issues/list?can=1&q=Milestone%3D1.7.1

New in 1.7:
-----------
* Switched to MySQL for the database, instead of MongoDb
* Solr based search interface
* Many user interface improvements

https://code.google.com/p/civic-crm/issues/list?can=1&q=Milestone%3D1.7