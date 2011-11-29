<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$discovery = array(
	'changeset'=>'2011-11-29T09:48Z-05',
	'contact'=>'You can email for assistance '.ADMINISTRATOR_EMAIL,
	'key_service'=>'You can request a key by emailing '.ADMINISTRATOR_EMAIL,
	'endpoints'=>array(
		array(
			'specification'=>'http://wiki.open311.org/GeoReport_v2',
			'url'=>BASE_URL.'/open311/v2',
			'changeset'=>'',
			'type'=>'development',
			'formats'=>array('text/xml','application/json')
		)
	)
);
echo json_encode($discovery);