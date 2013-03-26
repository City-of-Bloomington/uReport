uReport is a small scale, standalone, CRM web application with an Open311
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

Upcoming Changes in 1.8
----------------
The 1.8 release is officially being worked on.  The master branch *should* always stable, if you want to live with the latest code as it's committed.  Otherwise, you can wait for the official 1.8 release which will (hopefully) be tested a bit more and actually *be* stable.

The 1.8 release will involve a data migration.  There are numerous changes to the backend MySQL and Solr schemas.  The MySQL database will need to be modified, the new Solr schema put into place, and the Solr index re-indexed.  Code for the migration is in /scripts/migration/1.7-1.8.  The migration code will be continually updated as features are committed.  So, if you've run it once, you might check the scripts for new changes periodically.

After you grab the latest version of the code, it's in
`/scripts/migration/1.7-1.8`

There's an SQL script, and a new schema file for Solr.

The SQL script contains a bunch of SQL commands to run.  You might be
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

Once you've applied all the database changes, you'll need to update your
Solr index.  You'll need to replace your existing schema.xml with the
new one.  Then, you'll need to delete the data in your core and restart
Tomcat.

Once Tomcat restarts, you should be able to go back to
/uReport/scripts/solr
In there is a php script for re-indexing the Solr search engine. You
should be able to run that from the command line on your server.
```bash
cd ureport/scripts/solr
php indexSearch.php
```

For us, with 100,000 records, the search indexing takes about 10 minutes.

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