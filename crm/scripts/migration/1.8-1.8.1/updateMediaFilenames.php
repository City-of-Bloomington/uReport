<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include '../../../configuration.inc';

$list = new MediaList();
$list->find();
foreach ($list as $media) {
	$path = APPLICATION_HOME."/data/media/{$media->getDirectory()}";
	$old = "$path/{$media->getId()}.{$media->getExtension()}";
	$new = "$path/{$media->getInternalFilename()}";
	rename($old, $new);
	$media->save();
	echo "$old -> $new\n";
}
