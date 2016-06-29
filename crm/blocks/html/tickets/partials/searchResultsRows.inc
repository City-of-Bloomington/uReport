<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Apache_Solr_Response $this->solrObject
 */
use Application\Models\Person;
use Application\Models\Search;
use Application\Models\TicketTable;

$tickets = Search::hydrateDocs($this->solrObject);
echo '<div class="searchResultsRows">';
foreach ($tickets as $ticket) {
	// Go through each of the fields the user wants displayed
	// and dig the displayable value from the row data
	$displayableFields = TicketTable::getDisplayableFields();
	$additionalFields  = $ticket->getAdditionalFields();
	// Create empty variables needed for the final markup
	foreach ([ 'status', 'substatus_id',
               'location', 'city', 'state', 'zip',
               'category_id', 'description'] as $k) {
		$$k = '';
	}

	$dl = [];

	foreach ($_GET['fields'] as $field=>$value) {
		// Make sure the user is allowed to see this field
		if (array_key_exists($field, $displayableFields)) {
			// Format the data being asked for
			$get = 'get'.ucfirst($field);
			switch ($field) {
				case 'status':
				case 'location':
				case 'city':
				case 'state':
				case 'zip':
				case 'description':
					$$field = $ticket->$get() ? self::escape($ticket->$get()) : '';
					break;

				case 'substatus_id':
				case 'category_id':
					$g = 'get'.ucfirst(substr($field, 0, -3));
					$o = $ticket->$g();
					$$field = $o ? self::escape($o->getName()) : '';
					break;

				case 'enteredByPerson_id':
				case 'assignedPerson_id':
					$get = 'get'.ucfirst(substr($field, 0, -3));
					$person = $ticket->$get();
					if ($person) {
						$dl[$field] = self::escape($person->getFullname());
					}
					break;

				case 'enteredDate':
					$dl[$field] = $ticket->$get(DATE_FORMAT);
					break;


				case 'department_id':
					$department = $ticket->getDepartment();
					$dl[$field] = $department ? self::escape($department->getName()) : '';
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
						$dl[$field] = self::escape($additionalFields->$field);
					}
			}
		}
	}
	echo "
		<article>
            <header>
			<h1><a href=\"".BASE_URI."/tickets/view?ticket_id={$ticket->getId()}\">
					{$this->_('ticket')}
					# {$ticket->getId()} $category_id $status $substatus_id
				</a>
			</h1>
			<span>$location $city $state $zip</span>
	";
	if (count($dl)) {
		echo '<ul>';
		foreach ($dl as $field=>$value) {
			$dt = $displayableFields[$field];
			$dd = $value;
			echo "<li><label>$dt:</label> $dd</li>\n";
		}
		echo '</ul>';
	}
	echo "
            <p>$description</p>
		</article>
	";
}
echo "</div>";
