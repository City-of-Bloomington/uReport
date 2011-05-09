<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param $_GET location
 */
// Make sure we have the location in the system
$caseList = new caseList(array('location'=>$_GET['location']));

$template = new Template('locations');
$template->blocks['location-panel'][] = new Block(
	'locations/locationInfo.inc',array('location'=>$_GET['location'])
);
$template->blocks['case-panel'][] = new Block(
	'cases/caseList.inc',
	array(
		'caseList'=>$caseList,
		'title'=>'cases Associated with this Location'
	)
);

echo $template->render();
