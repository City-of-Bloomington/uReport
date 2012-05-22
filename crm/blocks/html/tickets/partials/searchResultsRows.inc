<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->tickets
 */
// Make sure Ticket Number is always displayed
$_GET['fields']['number'] = 1;

foreach ($this->tickets as $ticket) {
	echo '<div class="searchResultsRows">';
	// Go through each of the fields the user wants displayed
	// and dig the displayable value from the row data
	$displayableFields = TicketList::getDisplayableFields();
	// Create empty variables needed for the final markup
	foreach (array('number','status','resolution',
					'location','city','state','zip',
					'category','description') as $k) {
		$$k = '';
	}

	$dl = array();

	foreach ($_GET['fields'] as $field=>$value) {
		// Make sure the user is allowed to see this field
		if (array_key_exists($field,$displayableFields)
			&& (false === strpos($field,'Person') || userIsAllowed('people'))) {
			// Format the data being asked for
			$get = 'get'.ucfirst($field);
			switch ($field) {
				case 'number':
				case 'status':
				case 'resolution':
				case 'location':
				case 'city':
				case 'state':
				case 'zip':
				case 'description':
					$$field = $ticket->$get() ? View::escape($ticket->$get()) : '';
					break;

				case 'category':
					$category = $ticket->$get();
					$$field = $category ? View::escape($category->getName()) : '';
					break;

				case 'enteredByPerson':
				case 'assignedPerson':
				case 'referredPerson':
					$person = $ticket->$get();
					if ($person) {
						$dl[$displayableFields[$field]['displayName']] = View::escape($person->getFullname());
					}
					break;

				case 'enteredDate':
					$dl[$displayableFields[$field]['displayName']] = $ticket->$get(DATE_FORMAT);
					break;


				case 'department':
					$department = $ticket->$get();
					$dl[$displayableFields[$field]['displayName']] = $department
						? View::escape($department->getName())
						: '';
					break;

				case 'latitude':
				case 'longitude':
					$dl[$displayableFields[$field]['displayName']] = $ticket->$get();
					break;

				default:
					$data = $ticket->getData();
					$dl[$displayableFields[$field]['displayName']] = isset($data[$displayableFields[$field]['index']])
						? View::escape($data[$displayableFields[$field]['index']])
						: '';
			}
		}
	}
	echo "
		<div>
			<h3><a href=\"".BASE_URL."/tickets/view?ticket_id={$ticket->getId()}\">
					Case # $number $category $status $resolution
				</a>
			</h3>
			<span>$location $city $state $zip</span>
	";
	if (count($dl)) {
		echo '<ul>';
		foreach ($dl as $dt=>$dd) {
			$dt = View::escape($dt);
			$dd = View::escape($dd);
			echo "<li><label>$dt:</label> $dd</li>\n";
		}
		echo '</ul>';
	}
	echo "
		<p>$description</p>
		</div>
	</div>
	";
}
