<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param string $this->location
 */
if (userIsAllowed('people','view')) {
	echo '<div class="locationPeople">';
	$people = new PersonList(array('address'=>$this->location));
	if (count($people)) {
		echo "
		<h3>People</h3>
		<p> A list of people who've listed this as their address.
			Note: it might not be their current address.
		</p>
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
		if (count($owners)) {
			echo '<h3>Owners</h3><ul>';
			foreach ($owners as $name) {
				$name = View::escape($name);
				echo "<li>$name</li>";
			}
			echo '</ul>';
		}
	}
	echo '</div>';
}
