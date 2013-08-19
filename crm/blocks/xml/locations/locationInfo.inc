<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param string $this->location
 * @param bool $this->disableButtons
 */
if ($this->location) {
	$location = View::escape($this->location);

	echo "
	<location>
		<name>$location</name>
	";
	if (defined('ADDRESS_SERVICE')) {
		$data = AddressService::getLocationData($this->location);
		if (count($data)) {
			echo '<address>';
			foreach ($data as $key=>$value) {
				$value = View::escape(trim($value));
				if (array_key_exists($key,AddressService::$customFieldDescriptions)
					&& $value) {
					$label = AddressService::$customFieldDescriptions[$key]['description'];
					echo "<$label>$value</$label>";
				}
			}
			echo '</address>';
		}
	}

	if (userIsAllowed('People')) {
		$people = new PersonList(array('address'=>$this->location));
		if (count($people)) {
			echo '<people>';
			foreach ($people as $person) {
				$name = View::escape($person->getFullname());
				echo "<name>$name</name>";
			}
			echo '</people>';
		}

		if (defined('ADDRESS_SERVICE') && defined('RENTAL_SERVICE')) {
			$location = urlencode($this->location);
			$url = new URL(RENTAL_SERVICE."?streetAddress=$location&type=xml");
			$xml = simplxml_load_string(URL::get($url));
			$owners = $xml->xpath("//Owner");
			if (count($owners)) {
				echo '<owners>';
				foreach ($owners as $owner) {
					echo "<owner>{$owner->Name}</owner>";
				}
				echo '</owners>';
			}
		}
	}

	echo "
	</location>
	";
}
