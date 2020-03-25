<?php
/**
 * @copyright 2011-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Apache_Solr_Response $this->solrObject
 * @param array                $this->fields      Ticket fields the user asked to see
 */
use Application\Models\Search;
use Application\Models\TicketTable;

$OUT               = fopen('php://output', 'w');
$tickets           = Search::hydrateDocs($this->solrObject);
$displayableFields = TicketTable::getDisplayableFields();

foreach ($tickets as $ticket) {
    $line              = [];
	$additionalFields  = $ticket->getAdditionalFields();
	
    // Go through each of the fields the user wants displayed
    // and dig the displayable value from the row data
	foreach ($this->fields as $field=>$value) {
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
					$value = $ticket->$get();
					break;

				case 'substatus_id':
				case 'category_id':
					$g     = 'get'.ucfirst(substr($field, 0, -3));
					$o     = $ticket->$g();
					$value = $o ? $o->getName() : '';
					break;

				case 'enteredByPerson_id':
				case 'assignedPerson_id':
					$get    = 'get'.ucfirst(substr($field, 0, -3));
					$person = $ticket->$get();
					$value  = $person ? $person->getFullname() : '';
					break;

				case 'enteredDate':
					$value = $ticket->$get(DATE_FORMAT);
					break;


				case 'department_id':
					$department = $ticket->getDepartment();
					$value      = $department ? $department->getName() : '';
					break;

				case 'latitude':
				case 'longitude':
					$value = $ticket->$get();
					break;

				case 'slaPercentage':
					$value = $ticket->$get();
					break;

				default:
					// Anything else should be a custom field for the ticket
					if (isset($additionalFields->$field)) {
						$value = self::escape($additionalFields->$field);
					}
			}
			$line[] = $value;
		}
	}
	fputcsv($OUT, $line);
}
fclose($OUT);
