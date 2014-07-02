<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Apache_Solr_Response $this->solrObject
 */
$tickets = Search::hydrateDocs($this->solrObject);
echo '<div class="searchResultsRows">';
foreach ($tickets as $ticket) {
	// Go through each of the fields the user wants displayed
	// and dig the displayable value from the row data
	$displayableFields = TicketList::getDisplayableFields();
	$additionalFields = $ticket->getAdditionalFields();
	// Create empty variables needed for the final markup
	foreach (array( 'status', 'substatus_id',
					'location', 'city', 'state', 'zip',
					'category_id', 'description') as $k) {
		$$k = '';
	}

	$dl = array();

	foreach ($_GET['fields'] as $field=>$value) {
		// Make sure the user is allowed to see this field
		if (array_key_exists($field, $displayableFields)
			&& (false === strpos($field,'Person') || Person::isAllowed('people'))) {
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

				case 'substatus_id':
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
						$dl[$field] = View::escape($person->getFullname());
					}
					break;

				case 'enteredDate':
					$dl[$field] = $ticket->$get(DATE_FORMAT);
					break;


				case 'department_id':
					$department = $ticket->getDepartment();
					$dl[$field] = $department ? View::escape($department->getName()) : '';
					break;

				case 'latitude':
				case 'longitude':
					$dl[$field] = $ticket->$get();
					break;

				case 'slaPercentage':
					$sla = $ticket->$get();
					if ($sla) {
						$class = $sla > 100 ? 'pastDue' : 'onTrack';
						$dl[$field] = "<span class=\"$class\">$sla%</span>";
					}
					break;

				default:
					// Anything else should be a custom field for the ticket
					if (isset($additionalFields->$field)) {
						$dl[$field] = View::escape($additionalFields->$field);
					}
			}
		}
	}
	echo "
		<div>
			<h3><a href=\"".BASE_URI."/tickets/view?ticket_id={$ticket->getId()}\">
					{$this->translate(array('labels.ticket', 'labels.tickets', 1))}
					# {$ticket->getId()} $category_id $status $substatus_id
				</a>
			</h3>
			<span>$location $city $state $zip</span>
	";
	if (count($dl)) {
		echo '<ul class="twoColumnlist">';
		foreach ($dl as $field=>$value) {
			$dt = $displayableFields[$field];
			$dd = $value;
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
