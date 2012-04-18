<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param string $this->location
 * @param bool $this->disableButtons
 */
if ($this->location) {
	$url = new URL(BASE_URL.'/locations/view?location='.$this->location);
	$location = View::escape($this->location);

	echo "
	<div class=\"locationInfo\">
		<h2><a href=\"$url\" class=\"location\">$location</a></h2>
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

	if (userIsAllowed('people','view')) {
		$people = new PersonList(array('address'=>$this->location));
		if (count($people)) {
			echo "<h3>People</h3>
			<p>A list of people who've listed this as their address. Note: it might not be their current address.</p> <ul>";
			foreach ($people as $person) {
				$name = View::escape($person->getFullname());
				echo "<li><a href=\"{$person->getURL()}\">$name</a></li>";
			}
			echo "</ul>";
		}

		if (defined('ADDRESS_SERVICE') && defined('RENTAL_SERVICE')) {
			$location = urlencode($this->location);
			$url = RENTAL_SERVICE."?streetAddress=$location&type=xml";
			$xml = new SimpleXMLElement($url,null,true);
			$owners = $xml->xpath("//Owner");
			if (count($owners)) {
				echo '<h3>Owners</h3><ul>';
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
