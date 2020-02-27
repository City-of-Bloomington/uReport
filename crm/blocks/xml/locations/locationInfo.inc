<?php
/**
 * @copyright 2011-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param string $this->location
 * @param bool $this->disableButtons
 */
use Application\Models\AddressService;
use Application\Models\Person;
use Application\Models\RentalService;
use Blossom\Classes\View;

if ($this->location) {
	$location = self::escape($this->location);

	echo "
	<location>
		<name>$location</name>
	";
	if (defined('ADDRESS_SERVICE')) {
		$data = AddressService::getLocationData($this->location);
		if (count($data)) {
			echo '<address>';
			foreach ($data as $key=>$value) {
				$value = self::escape(trim($value));
				if (array_key_exists($key,AddressService::$customFieldDescriptions)
					&& $value) {
					$label = AddressService::$customFieldDescriptions[$key]['description'];
					echo "<$label>$value</$label>";
				}
			}
			echo '</address>';
		}
	}

	if (Person::isAllowed('People')) {
		$people = new PersonList(array('address'=>$this->location));
		if (count($people)) {
			echo '<people>';
			foreach ($people as $person) {
				$name = self::escape($person->getFullname());
				echo "<name>$name</name>";
			}
			echo '</people>';
		}

		if (defined('RENTAL_SERVICE')) {
			$owners = RentalService::getOwnerNames($this->location);
			if (count($owners)) {
				echo '<owners>';
				foreach ($owners as $name) {
					$name = self::escape($name);
					echo "<owner>$name</owner>";
				}
				echo '</owners>';
			}
		}
	}

	echo "
	</location>
	";
}
