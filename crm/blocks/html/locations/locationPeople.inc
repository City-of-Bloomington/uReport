<?php
/**
 * @copyright 2012-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param string $this->location
 */
use Application\Models\Person;
use Application\Models\PersonTable;
use Application\Models\RentalService;

if ($this->location
    && Person::isAllowed('people', 'view')) {
    
	echo '<div class="locationPeople">';
	$table = new PersonTable();
	$people = $table->find(['address' => $this->location]);
	if (count($people)) {
        $title = $this->_(['person', 'people', count($people)]);
		echo "
		<h2>$title</h2>
		<p>{$this->_('locationPeople_description', 'messages')}</p>
		<ul>
		";
		foreach ($people as $person) {
			$name = self::escape($person->getFullname());
			echo "<li><a href=\"{$person->getURL()}\">$name</a></li>";
		}
		echo "</ul>";
	}

	if (defined('RENTAL_SERVICE')) {
		$owners = RentalService::getOwnerNames($this->location);
		$count = count($owners);
		if ($count) {
			echo "<h2>{$this->_(['owner', 'owners', $count])}</h2><ul>";
			foreach ($owners as $name) {
				$name = self::escape($name);
				echo "<li>$name</li>";
			}
			echo '</ul>';
		}
	}
	echo '</div>';
}
