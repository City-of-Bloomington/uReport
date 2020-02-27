<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$lastModified = '2012-09-18T15:37-05';
$discovery = array(
	'changeset'=>$lastModified,
	'contact'=>'You can email for assistance '.ADMINISTRATOR_EMAIL,
	'key_service'=>'You can request a key at '.OPEN311_KEY_SERVICE,
	'endpoints'=>array(
		array(
			'specification'=>'http://wiki.open311.org/GeoReport_v2',
			'url'=>BASE_URL.'/open311/v2',
			'changeset'=>$lastModified,
			'type'=>'production',
			'formats'=>array('text/xml','application/json')
		),
		array(
			'specification'=>'http://wiki.open311.org/GeoReport_v2',
			'url'=>'https://bloomington.in.gov/test/open311/v2',
			'changeset'=>$lastModified,
			'type'=>'test',
			'formats'=>array('text/xml','application/json')
		)
	)
);
echo json_encode($discovery);
