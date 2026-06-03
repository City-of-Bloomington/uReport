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
define('BASE_URI' , '');
define('BASE_HOST', 'localhost:8080');
define('BASE_URL' , 'http://localhost:8080');

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
define('GOOGLE_MAPS',   'http://maps.googleapis.com/maps/api/js?sensor=false');
define('GOOGLE_LOADER', 'http://www.google.com/jsapi');

/**
 * Database Setup
 */
$DATABASES = [
    'default' => [
        'dsn'     =>sprintf(
            'mysql:host=%s;port=%s;dbname=%s',
            getenv('DB_HOST'),
            getenv('DB_PORT'),
            getenv('DB_NAME')),
        'user'    => getenv('DB_USER'),
        'pass'    => getenv('DB_PASS'),
        'options' => []
    ]
];

$AUTHENTICATION = [
    'oidc' => [
        'server'         => 'https://ad.example.org/adfs',
        'client_id'      => '',
        'client_secret'  => '',
        'claims' => [
            // OnBoard field => OIDC Claim
            'username'    => 'adfs1upn',
            'displayname' => 'commonname',
            'firstname'   => 'given_name',
            'lastname'    => 'family_name',
            'email'       => 'upn',
            'groups'      => 'group',
            'groupmap'    => [ ]
        ],
    ]
];

/**
 * Controls whether the system sends email notifications to people
 */
define('NOTIFICATIONS_ENABLED', true);
define('SMTP_HOST', 'localhost.localdomain');
define('SMTP_PORT', 25);
define('ADMINISTRATOR_EMAIL', 'someone@localhost');

/**
 * Point to the Solr server
 */
$SOLR = [
    'ureport' => [
        'scheme'   => 'https',
        'host'     => 'localhost',
        'port'     => 443,
        'core'     => 'ureport',
        'username' => 'ureport',
        'password' => 'secret password'
    ]
];

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
