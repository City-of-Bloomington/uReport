<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param string $this->location
 */
if ($this->location) {
	$url = new URL(BASE_URL.'/locations/viewLocation.php?location='.$this->location);
	$location = View::escape($this->location);

	echo "
	<div class=\"locationInfo\">
		<h1><a href=\"$url\">$location</a></h1>
	";
	if (defined('ADDRESS_SERVICE')) {
		$data = AddressService::getLocationData($this->location);
		if (count($data)) {
			echo '<table>';
			foreach ($data as $key=>$value) {
				$value = View::escape(trim($value));
				if (array_key_exists($key,AddressService::$customFieldDescriptions)
					&& $value) {
					$label = AddressService::$customFieldDescriptions[$key]['description'];
					echo "<tr><th>$label</th><td>$value</td></tr>";
				}
			}
			echo '</table>';
		}
	}
	
	if (userIsAllowed('Tickets') && !$this->disableButtons) {
		echo $this->linkButton(
			'Report New Case',BASE_URL.'/tickets/addTicket.php?location='.urlencode($location),
			'add',null,null,'button'
		);
	}

	$people = new PersonList(array('address'=>$this->location));
	if (count($people)) {
		echo "<ul>";
		foreach ($people as $person) {
			$name = View::escape($person->getFullname());
			echo "<li><a href=\"{$person->getURL()}\">$name</a></li>";
		}
		echo "</ul>";
	}
}

echo "
	</div>
	";