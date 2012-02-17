<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TicketList $this->ticketList
 */
foreach ($this->ticketList as $row) {
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
			&& (false === strpos($field,'Person') || userIsAllowed('People'))) {
			// Format the data being asked for
			switch ($field) {
				case 'number':
				case 'status':
				case 'resolution':
				case 'location':
				case 'city':
				case 'state':
				case 'zip':
					$$field = isset($row[$field]) ? View::escape($row[$field]) : '';
					break;

				case 'category':
					$$field = isset($row['category']['name'])
						? View::escape($row['category']['name'])
						: '';
					break;

				case 'description':
					$$field = isset($row['issues'][0]['description'])
						? View::escape($row['issues'][0]['description'])
						: '';
					break;

				case 'enteredByPerson':
				case 'assignedPerson':
				case 'referredPerson':
					if (isset($row[$field])) {
						$person = new Person($row[$field]);
						$dl[$displayableFields[$field]['displayName']] = View::escape($person->getFullname());
					}
					break;

				case 'enteredDate':
					if (isset($row[$field]->sec)) {
						$dl[$displayableFields[$field]['displayName']] = date(DATE_FORMAT,$row[$field]->sec);
					}
					break;


				case 'department':
					$dl[$displayableFields[$field]['displayName']] = isset($row['assignedPerson']['department']['name'])
						? View::escape($row['assignedPerson']['department']['name'])
						: '';
					break;

				case 'latitude':
				case 'longitude':
					$dl[$displayableFields[$field]['displayName']] = isset($row['coordinates'][$field])
						? $row['coordinates'][$field]
						: '';
					break;

				default:
					$dl[$displayableFields[$field]['displayName']] = isset($row[$displayableFields[$field]['index']])
						? View::escape($row[$displayableFields[$field]['index']])
						: '';
			}
		}
	}
	echo "
		<div>
			<a href=\"".BASE_URL."/tickets/view?ticket_id=$row[_id]\">$number</a>
			$category, $status, $location, $city, $state, $zip
			<p>$description</p>
	";
	if (count($dl)) {
		echo '<dl>';
		foreach ($dl as $dt=>$dd) {
			$dt = View::escape($dt);
			$dd = View::escape($dd);
			echo "<dt>$dt</dt><dd>$dd</dd>\n";
		}
		echo '</dl>';
	}
	echo "
		</div>
	</div>
	";
}
