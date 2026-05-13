<?php
/**
 * Where on the filesystem this application is installed
 */
define('APPLICATION_HOME', __DIR__);
define('BLOSSOM', APPLICATION_HOME.'/vendor/City-of-Bloomington/blossom-lib');
define('VERSION', trim(file_get_contents(APPLICATION_HOME.'/VERSION')));

/**
 * Data Directory
 *
 * SITE_HOME is the directory where all site-specific data and
 * configuration are stored.  For backup purposes, backing up this
 * directory would be sufficient for an easy full restore.
 */
define('SITE_HOME', !empty($_SERVER['SITE_HOME']) ? $_SERVER['SITE_HOME'] : __DIR__.'/data');

$loader = require APPLICATION_HOME.'/vendor/autoload.php';
$loader->addPsr4('Application\\', APPLICATION_HOME);
$loader->addPsr4('Site\\', SITE_HOME);

include SITE_HOME.'/site_config.php';
include APPLICATION_HOME.'/access_control.php';

/**
 * Graylog is a centralized log manager
 *
 * This application supports sending errors and exceptions to a graylog instance.
 * This is handy for notifying developers of a problem before users notice.
 * @see https://graylog.org
 */
if (defined('GRAYLOG_DOMAIN') && defined('GRAYLOG_PORT')) {
             set_error_handler('Application\GraylogWriter::error');
         set_exception_handler('Application\GraylogWriter::exception');
    register_shutdown_function('Application\GraylogWriter::shutdown');
}

/**
 * Image handling library
 * Set the path to the ImageMagick binaries
 */
define('IMAGEMAGICK','/usr/bin');
