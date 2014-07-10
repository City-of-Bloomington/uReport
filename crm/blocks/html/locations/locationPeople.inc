<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param string $this->location
 */
use Application\Models\Person;
use Application\Models\PersonTable;
use Application\Models\RentalService;
use Blossom\Classes\View;

if (Person::isAllowed('people','view')) {
	echo '<div class="locationPeople">';
	$table = new PersonTable();
	$people = $table->find(['address' => $this->location]);
	if (count($people)) {
		echo "
		<h3>{$this->translate(array('labels.person', 'labels.people', 2))}</h3>
		<p>{$this->translate('text.locationPeople_description')}</p>
		<ul>
		";
		foreach ($people as $person) {
			$name = View::escape($person->getFullname());
			echo "<li><a href=\"{$person->getURL()}\">$name</a></li>";
		}
		echo "</ul>";
	}

	if (defined('RENTAL_SERVICE')) {
		$owners = RentalService::getOwnerNames($this->location);
		$count = count($owners);
		if ($count) {
			echo "<h3>{$this->translate(array('labels.owner', 'labels.owners', $count))}</h3><ul>";
			foreach ($owners as $name) {
				$name = View::escape($name);
				echo "<li>$name</li>";
			}
			echo '</ul>';
		}
	}
	echo '</div>';
}
