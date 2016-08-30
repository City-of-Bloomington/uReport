uReport is a CRM web application with an Open311
(GeoReport v2) endpoint that should meet the needs of smaller municipalities
wishing to deploy Open311 and/or a lightweight constituent management tool.
Context-switched feeds (XML, JSON, etc) allow it to easily be integrated into
existing environments.

## Installation
In order to install one of our binary releases, you must have a linux system already set up with:

* [PHP 5.6](http://php.net) or later
* [Apache](http://httpd.apache.org)
* [MySQL](http://dev.mysql.com)
* [Solr](http://lucene.apache.org/solr)

There are many ways to set up and install your own linux webserver.  Our [github page](http://city-of-bloomington.github.io) documents how we install linux web applications here in Bloomington.  Our way is not the only way, though.  It's well worth reading up on all the technologies and deciding what you need for your own hosting.

Once you've got hosting sorted out, you can follow the [Wiki instructions](https://github.com/City-of-Bloomington/uReport/wiki/Install) to install uReport on your webserver.

## Developing uReport
We are always open to new collaborators.  If you are customizing uReport, we welcome pull requests on Github.

To get started developing uReport, there are some additional requirements in order to build a working copy from source.

* composer
* sass
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
Details are in [LICENSE.txt](LICENSE.txt)
