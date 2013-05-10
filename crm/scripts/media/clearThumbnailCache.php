<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
require_once '../../configuration.inc';

$files = glob(APPLICATION_HOME.'/data/media/*/*/*/*/*/');
foreach ($files as $file) {
	unlink($file);
	echo "Removed $file\n";
}

$directories = glob(APPLICATION_HOME.'/data/media/*/*/*/*/', GLOB_ONLYDIR);
foreach ($directories as $dir) {
	rmdir($dir);
	echo "Removed $dir\n";
}
