<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
require_once '../../../configuration.inc';

// Point to a directory containing all the media from the old server
define('OLD_MEDIA_PATH', '/var/www/sites/crm/data/media');

// Connect to the old Mongo database
// Make sure to open up the mongo port (27017) in the firewall
$connection = new Mongo('mongodb://rogue.bloomington.in.gov');
$mongo = $connection->crm;

// Connect to the new MySQL database
$zend_db = Database::getConnection();

$sql = "create table people_crosswalk (
	person_id int unsigned not null,
	mongo_id varchar(24) not null,
	primary key (person_id, mongo_id),
	foreign key (person_id) references people(id)
)";
#$zend_db->query($sql);
function getPersonIdFromCrosswalk($mongo_id)
{
	global $zend_db;
	$sql = 'select person_id from people_crosswalk where mongo_id=?';
	return $zend_db->fetchOne($sql, array((string)$mongo_id));
}
