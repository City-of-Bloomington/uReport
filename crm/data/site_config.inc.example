<?php
define('APPLICATION_NAME','CRM');

/**
 * URL Generation settings
 *
 * Do NOT use trailing slashes
 *
 * If your site is being proxied, change BASE_HOST to the hostname
 * used for the outside world.
 */
define('BASE_URI' , '/application');
#define('BASE_HOST', isset($_SERVER['HTTP_X_FORWARDED_HOST'])
#                        ? $_SERVER['HTTP_X_FORWARDED_HOST']
#                        : $_SERVER['SERVER_NAME']);
define('BASE_HOST', $_SERVER['SERVER_NAME']);
define('BASE_URL' , "$_SERVER[REQUEST_SCHEME]://".BASE_HOST.BASE_URI);

/**
 * Specify the theme directory
 *
 * Remember to create a symbolic link in public/css to the theme CSS
 * that you declare here.
 *
 * A theme can consist of templates, blocks which will override core.
 * The current theme's screen.css will be included in the HTML head.
 */
define('THEME', 'COB');

/**
 * JavaScript Libraries
 */
define('JQUERY',      BASE_URI.'/js/vendor/jquery-2.1.1');
define('JQUERY_UI',   BASE_URI.'/js/vendor/jquery-ui-1.11.1');
define('GOOGLE_MAPS',   'http://maps.googleapis.com/maps/api/js?sensor=false');
define('GOOGLE_LOADER', 'http://www.google.com/jsapi');

/**
 * Database Setup
 */
$DATABASES = [
    'default' => [
        'driver'  => 'mysql',
        'host'    => 'localhost',
        'name'    => 'ureport',
        'user'    => 'ureport',
        'pass'    => 'password',
        'options' => []
    ]
];

/**
 * Directory Configuration
 *
 * This supports doing user authentication from multiple external
 * directories, such as LDAP or ADS. This is required since city staff
 * are in a seperate LDAP directory from public user accounts.
 * Classes that implement ExternalIdentity should have an entry here.
 *
 * See: ExternalIdentity
 */
// Example for ADS style authentication
$DIRECTORY_CONFIG = [
//  'Ldap' => [
//      'classname' => 'Site\Classes\Ldap',
//      'DIRECTORY_SERVER'=>'ldaps://example.org:636',
//      'DIRECTORY_BASE_DN'=>'OU=Department,DC=example,DC=org',
//      'DIRECTORY_USERNAME_ATTRIBUTE'=>'CN',
//      'DIRECTORY_USER_BINDING'=>'{username}@example.org',
//      'DIRECTORY_ADMIN_BINDING'=>'admin@example.org',
//      'DIRECTORY_ADMIN_PASS'=>'password'
//  ]
//
// Example for LDAP style authentication
//  'Ldap' => [
//      'classname' => 'Site\Classes\Ldap',
//      'DIRECTORY_SERVER'=>'ldaps://example.org:636');
//      'DIRECTORY_BASE_DN'=>'ou=people,o=ldap.domain.somewhere');
//      'DIRECTORY_USERNAME_ATTRIBUTE'=>'uid');
//      'DIRECTORY_USER_BINDING'=>'uid={username},'.DIRECTORY_BASE_DN);
//      'DIRECTORY_ADMIN_BINDING'=>'uid=admin,'.DIRECTORY_BASE_DN);
//      'DIRECTORY_ADMIN_PASS'=>'password');
//  ]
// Example for Directory webservice
//  'Employee' => [
//      'classname' => 'Site\Classes\Employee',
//      'DIRECTORY_SERVER' => 'http://localhost/directory',
//  ]
];

/**
 * CAS authentication http://www.jasig.org/cas
 *
 * https://wiki.jasig.org/display/CASC/phpCAS
 *
 * phpCAS is a PHP library for handling the calls to the CAS service
 * It is the official library, part of the Jasig CAS project
 */
//define('CAS', APPLICATION_HOME.'/vendor/jasig/phpcas/source');
//define('CAS_SERVER','cas.somewhere.org');
//define('CAS_URI','cas');

/**
 * Auth0 authentication https://auth0.com
 *
 * Comment this out to disable Auth0 authentication
 * Uncomment to enable
 */
//$AUTH0 = [
//    'domain'        => 'somewhere.auth0.com',
//    'client_id'     => 'HASH CODE',
//    'client_secret' => 'SECRET API KEY'
//];

/**
 * Controls whether the system sends email notifications to people
 */
define('NOTIFICATIONS_ENABLED', true);
define('ADMINISTRATOR_EMAIL', 'someone@localhost');

/**
 * Point to the Solr server
 */
define('SOLR_SERVER_HOSTNAME', 'localhost');
define('SOLR_SERVER_PORT', 8983);
define('SOLR_SERVER_PATH', '/solr/ureport');


/**
 * Addresses can be validated against a master address web service
 */
//define('ADDRESS_SERVICE','http://localhost/master_address');

/**
 * Rental addresses have additional information we can look up
 * Owner information is being provided from this service
 */
//define('RENTAL_SERVICE','http://localhost/rentpro/RentService');

/**
 * Some default values for Tickets in the system
 */
define('DEFAULT_CITY','Bloomington');
define('DEFAULT_STATE','IN');

/**
 * Default coordinates for map center
 * This should probably be the center of your city
 * If you can, it's best to adjust these values in your php.ini
 */
define('DEFAULT_LATITUDE', ini_get('date.default_latitude'));
define('DEFAULT_LONGITUDE',ini_get('date.default_longitude'));
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
 * This is a unique string identifying your jurisdiction to the
 * rest of the Open311 community.
 *
 * Open311 servers typically use their domain name as their jurisdiction
 */
define('OPEN311_JURISDICTION','localhost');
define('OPEN311_KEY_SERVICE', 'http://localhost/open311-api-key-request');

define('THUMBNAIL_SIZE', 150);

define('CLOSING_COMMENT_REQUIRED_LENGTH', 1);
define('AUTO_CLOSE_COMMENT', 'Closed automatically');

define('DATE_FORMAT', 'n/j/Y');
define('DATETIME_FORMAT', 'n/j/Y H:i:s');
define('TIME_FORMAT', 'H:i:s');
define('LOCALE', 'en_US');

/**
 * Graylog is a centralized log manager
 *
 * This application supports sending errors and exceptions to a graylog instance.
 * This is handy for notifying developers of a problem before users notice.
 ** @see https://graylog.org
 */
#define('GRAYLOG_DOMAIN', 'graylog.example.org');
#define('GRAYLOG_PORT',   12201);
