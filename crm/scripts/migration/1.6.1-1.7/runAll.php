<?php
/**
 * Does a full import from the old Mongo database
 *
 * This should start from a completely empty database.
 * Remember to drop the database and reloading from scripts/mysql.sql
 * Everything we want should come from Mongo
 *
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$startTime = time();
require_once './config.inc';
include './1_lookups.php';
include './2_DepartmentsPeopleCategories.php';
include './3_Clients.php';
include './4_Tickets.php';


function getNiceDuration($durationInSeconds) {
	$duration = '';
	$days     = floor($durationInSeconds / 86400);
	$durationInSeconds -= $days * 86400;
	$hours    = floor($durationInSeconds / 3600);
	$durationInSeconds -= $hours * 3600;
	$minutes  = floor($durationInSeconds / 60);
	$seconds  = $durationInSeconds - $minutes * 60;

	if($days > 0) {
		$duration .= $days . ' days';
	}
	if($hours > 0) {
		$duration .= ' ' . $hours . ' hours';
	}
	if($minutes > 0) {
		$duration .= ' ' . $minutes . ' minutes';
	}
	if($seconds > 0) {
		$duration .= ' ' . $seconds . ' seconds';
	}
	return $duration;
}
$endTime = time();
$duration = $endTime - $startTime;
echo getNiceDuration($duration);
