<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Array $this->results
 */
$locations = array();
foreach ($this->results as $location=>$street_address_id) {
	$location = array('location'=>$location);
	if ($street_address_id) {
		$location['street_address_id'] = $street_address_id;
	}
	$locations[] = $location;
}
echo json_encode($locations);