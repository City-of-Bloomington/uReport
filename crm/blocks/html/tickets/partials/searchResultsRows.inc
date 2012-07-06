<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->tickets
 */
echo '<div class="searchResultsRows">';
foreach ($this->tickets as $ticket) {
	// Go through each of the fields the user wants displayed
	// and dig the displayable value from the row data
	$displayableFields = TicketList::getDisplayableFields();
	$additionalFields = $ticket->getAdditionalFields();
	// Create empty variables needed for the final markup
	foreach (array( 'status', 'resolution_id',
					'location', 'city', 'state', 'zip',
					'category_id', 'description') as $k) {
		$$k = '';
	}

	$dl = array();

	foreach ($_GET['fields'] as $field=>$value) {
		// Make sure the user is allowed to see this field
		if (array_key_exists($field, $displayableFields)
			&& (false === strpos($field,'Person') || userIsAllowed('people'))) {
			// Format the data being asked for
			$get = 'get'.ucfirst($field);
			switch ($field) {
				case 'status':
				case 'location':
				case 'city':
				case 'state':
				case 'zip':
				case 'description':
					$$field = $ticket->$get() ? View::escape($ticket->$get()) : '';
					break;

				case 'resolution_id':
				case 'category_id':
					$g = 'get'.ucfirst(substr($field, 0, -3));
					$o = $ticket->$g();
					$$field = $o ? View::escape($o->getName()) : '';
					break;

				case 'enteredByPerson_id':
				case 'assignedPerson_id':
				case 'referredPerson_id':
					$get = 'get'.ucfirst(substr($field, 0, -3));
					$person = $ticket->$get();
					if ($person) {
						$dl[$field] = $person->getFullname();
					}
					break;

				case 'enteredDate':
					$dl[$field] = $ticket->$get(DATE_FORMAT);
					break;


				case 'department_id':
					$department = $ticket->getDepartment();
					$dl[$field] = $department ? $department->getName() : '';
					break;

				case 'latitude':
				case 'longitude':
					$dl[$field] = $ticket->$get();
					break;

				default:
					// Anything else should be a custom field for the ticket
					if (isset($additionalFields->$field)) {
						$dl[$field] = $additionalFields->$field;
					}
			}
		}
	}
	echo "
		<div>
			<h3><a href=\"".BASE_URL."/tickets/view?ticket_id={$ticket->getId()}\">
					Case # {$ticket->getId()} $category_id $status $resolution_id
				</a>
			</h3>
			<span>$location $city $state $zip</span>
	";
	if (count($dl)) {
		echo '<ul class="twoColumnlist">';
		foreach ($dl as $field=>$value) {
			$dt = $displayableFields[$field];
			$dd = View::escape($value);
			echo "<li><label>$dt:</label> $dd</li>\n";
		}
		echo '</ul>';
	}
	echo "
		<p>$description</p>
		</div>
	";
}
echo "</div>";