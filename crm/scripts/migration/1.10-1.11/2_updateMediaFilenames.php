<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\MediaTable;
use Blossom\Classes\Database;

include '../../../bootstrap.inc';

// Clear out the thumbnail cache
$files = glob(APPLICATION_HOME.'/data/media/*/*/*/*/*');
foreach ($files as $file) {
	unlink($file);
	echo "Removed $file\n";
}

$directories = glob(APPLICATION_HOME.'/data/media/*/*/*/*/', GLOB_ONLYDIR);
foreach ($directories as $dir) {
	rmdir($dir);
	echo "Removed $dir\n";
}

// Remove the three letter extension from internalFilenames
$zend_db = Database::getConnection();
$query = $zend_db->createStatement('update media set internalFilename=? where id=?');

$table = new MediaTable();
$list = $table->find();
foreach ($list as $m) {
    $internalFilename = substr($m->getInternalFilename(), 0, 13);

 	$old = $m->getFullPath();
 	$new = dirname($old).'/'.$internalFilename;

 	$query->execute([$internalFilename, $m->getId()]);
	rename($old, $new);
	echo "$old -> $new\n";
}
