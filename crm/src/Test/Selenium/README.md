Selenium Tests
==============

These are integration and acceptance tests to be run against a production server.  We use these to test the site after a new production deployment.

These tests do make changes to the database, send emails, etc.  They should clean up after themselves; however, you may need to manually clean out test tickets if there are errors during the tests.

These tests rely on the [Facebook php-webdriver](https://github.com/facebook/php-webdriver) library.

Installing Selenium
-------------------

To run these, you will need to download the [Selenium Standalone Server](https://www.seleniumhq.org/download), and the [ChromeDriver](https://sites.google.com/a/chromium.org/chromedriver/downloads).  The chrome web driver must be copied somewhere available in your PATH environment.

Running the tests
-----------------
Start the "Selenium Standalone Server", then use phpunit to execute the tests.
```bash
java -jar selenium-server-standalone.jar
```

```bash
phpunit src/Test/Selenium/TestSomething.php
```
