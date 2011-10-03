<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TicketList $this->ticketList
 */
foreach ($this->ticketList as $row) {
	// Go through each of the fields the user wants displayed
	// and dig the displayable value from the row data
	$displayableFields = TicketList::getDisplayableFields();
	$line = array();
	foreach ($_GET['fields'] as $field=>$value) {
		// Make sure the user is allowed to see this field
		if (array_key_exists($field,$displayableFields)
			&& (false === strpos($field,'Person') || userIsAllowed('People'))) {
			// Format the data being asked for
			switch ($field) {
				case 'enteredByPerson':
				case 'assignedPerson':
				case 'referredPerson':
					if (isset($row[$field])) {
						$person = new Person($row[$field]);
						$value = $person->getFullname();
					}
					else {
						$value = '';
					}
					break;

				case 'enteredDate':
					if (isset($row[$field]->sec)) {
						$value = date(DATE_FORMAT,$row[$field]->sec);
					}
					break;

				case 'description':
					$value = isset($row['issues'][0]['description']) ? $row['issues'][0]['description'] : '';
					$value = preg_replace('/\s+/',' ',$value);
					break;
				case 'category':
					$value = isset($row['category']['name']) ? $row['category']['name'] : '';
					break;

				case 'department':
					$value = isset($row['assignedPerson']['department']['name'])
						? $row['assignedPerson']['department']['name']
						: '';
					break;
				case 'latitude':
				case 'longitude':
					$value = isset($row['coordinates'][$field]) ? $row['coordinates'][$field] : '';
					break;

				default:
					$value = isset($row[$displayableFields[$field]['index']])
						? $row[$displayableFields[$field]['index']]
						: '';
			}
			$line[] = $value;
		}
	}
	echo implode('|',$line)."\n";
}
