<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param string $this->location
 * @param bool $this->disableButtons
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

	if (userIsAllowed('People')) {
		$people = new PersonList(array('address'=>$this->location));
		if (count($people)) {
			echo "<h1>People</h1><ul>";
			foreach ($people as $person) {
				$name = View::escape($person->getFullname());
				echo "<li><a href=\"{$person->getURL()}\">$name</a></li>";
			}
			echo "</ul>";
		}

		if (defined('ADDRESS_SERVICE') && defined('RENTAL_SERVICE')) {
			$url = RENTAL_SERVICE."?streetAddress=$location&type=xml";
			$xml = new SimpleXMLElement($url,null,true);
			$owners = $xml->xpath("//Owner");
			if (count($owners)) {
				echo '<h1>Owners</h1><ul>';
				foreach ($owners as $owner) {
					echo "<li>{$owner->Name}</li>";
				}
				echo '</ul>';
			}
		}
	}

	echo "
	</div>
	";
}
