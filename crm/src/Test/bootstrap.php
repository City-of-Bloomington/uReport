<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
$_SERVER['SITE_HOME'] = __DIR__.'/data';
include realpath(__DIR__.'/../../bootstrap.php');

$GLOBALS['DATABASES'] = $DATABASES;
