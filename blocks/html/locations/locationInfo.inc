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

	$constituents = new PersonList(array('address'=>$this->location));
	if (count($constituents)) {
		echo "
		<div class=\"locationResidents\">
			<h2>Associated Residents</h2>
			<ul>
			";
				foreach ($constituents as $person) {
					$url = BASE_URL.'/people/viewPerson.php?person_id='.$person->getId();
					$name = View::escape($person->getFullname());
					echo "<li><a href=\"$url\">$name</a></li>";
				}
			echo "
			</ul>
		</div>
		";
	}
}

echo "
	</div>
	";