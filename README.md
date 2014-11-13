uReport is a CRM web application with an Open311
(GeoReport v2) endpoint that should meet the needs of smaller municipalities
wishing to deploy Open311 and/or a lightweight constituent management tool.
Context-switched feeds (XML, JSON, etc) allow it to easily be integrated into
existing environments.

System Requirements
----------------------
* [PHP 5.4](http://php.net) or later
* [Apache](http://httpd.apache.org)
* [MySQL](http://dev.mysql.com)
* [Tomcat](http://tomcat.apache.org)
* [Solr](http://lucene.apache.org/solr)

### Additional libraries ###
uReport also requires a few other vendor libraries.  If you download a binary
release, the extra libraries are already included.

The git repository has the vendor libraries included as git submodules.  If
you clone the repository, you will need to update the submodules to have git
download them into your source code.

```bash
git clone https://github.com/City-of-Bloomington/uReport.git
cd uReport
git submodule update -i
```

Questions and Feedback
----------------------
Online documentation is in this project's
[Wiki](https://github.com/City-of-Bloomington/uReport/wiki)

If you find this software acting in unexpected ways, please create an issue
in our [Issue Tracker](https://github.com/City-of-Bloomington/uReport/issues).

If you want to contribute to the project, join us on our
[Google Group](https://groups.google.com/forum/?fromgroups#!forum/ureport)

License
----------------------
The files in this project are released under the GNU Affero GPLv3.
Details are in [LICENSE.txt](LICENSE.txt)
