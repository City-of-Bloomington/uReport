uReport is a CRM web application with an Open311
(GeoReport v2) endpoint that should meet the needs of smaller municipalities
wishing to deploy Open311 and/or a lightweight constituent management tool.
Context-switched feeds (XML, JSON, etc) allow it to easily be integrated into
existing environments.

## Installation
In order to install one of our binary releases, you must have a linux system already set up with:

* [PHP    7.4](http://php.net) or later
* [Apache 2.4](http://httpd.apache.org)
* [MySQL  5.7](http://dev.mysql.com) or later
* [Solr   7.4](http://lucene.apache.org/solr)

There are many ways to set up and install your own linux webserver.  Our way is not the only way, though.  It's well worth reading up on all the technologies and deciding what you need for your own hosting.

Once you've got hosting sorted out, you can follow the [Wiki instructions](https://github.com/City-of-Bloomington/uReport/wiki/Install) to install uReport on your webserver.

## Running Tests

Tests are written using PHPUnit.  You can run them from the root installation directory.

Unit tests are safe run any time.  The do not alter the database or touch the hard drive of a deployment.

Integration tests are intended to be run against a production deployment to make sure everything is
configured correctly and working.  (Database connections, Image uploads, Solr queries, External webservices, etc.)  These tests will make queries and write files to the hard drive of the deployment.  However, they are non-destructive, read-only queries and are safe.

Database tests SHOULD NOT BE RUN against production.  These check the implementation of data apis and will alter and delete data.  Only run these against a dev or test instance.

```
SITE_HOME=/path/to/data/dir phpunit -c src/Test/Unit.xml
SITE_HOME=/path/to/data/dir phpunit -c src/Test/Integration.xml
SITE_HOME=/path/to/data/dir phpunit -c src/Test/Database.xml
```

## Developing uReport
We are always open to new collaborators.  If you are customizing uReport, we welcome pull requests on Github.

To get started developing uReport, there are some additional requirements in order to build a working copy from source.

* composer
* node-sass
* GNU gettext command line tools
* bash

## Questions and Feedback
Online documentation is in this project's
[Wiki](https://github.com/City-of-Bloomington/uReport/wiki)

If you find this software acting in unexpected ways, please create an issue
in our [Issue Tracker](https://github.com/City-of-Bloomington/uReport/issues).

If you want to contribute to the project, join us on our
[Google Group](https://groups.google.com/forum/?fromgroups#!forum/ureport)

## License
The files in this project are released under the GNU Affero GPLv3.
Details are in [LICENSE](LICENSE)
