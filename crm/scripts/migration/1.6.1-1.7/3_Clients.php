<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
require_once './config.inc';

// Clients
$result = $mongo->clients->find();
foreach ($result as $r) {
	$id = getPersonIdFromCrosswalk($r['_id']);

	$zend_db->insert('clients',array(
		'name'=>$r['name'],
		'url'=>$r['url'],
		'api_key'=>(string)$r['_id'],
		'contactPerson_id'=>$id
	));
	echo "Client: $r[name]\n";
}
