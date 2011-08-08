<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TicketList $this->ticketList
 * @param array $this->fieldsToDisplay
 */
echo "<tbody>";
foreach ($this->ticketList as $row) {
	echo '<tr>';
	// Go through each of the fields the user wants displayed
	// and dig the displayable value from the row data
	foreach ($this->fieldsToDisplay as $field=>$definition) {
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
				break;
			case 'category':
				$value = isset($row['category']['name']) ? $row['category']['name'] : '';
				break;

			case 'department':
				$value = isset($row['assignedPerson']['department']['name'])
					? View::escape($row['assignedPerson']['department']['name'])
					: '';
				break;

			default:
				$value = isset($row[$definition['sortOn']]) ? $row[$definition['sortOn']] : '';
		}
		if (false === strpos($field,'Person') || userIsAllowed('People')) {
			$value = View::escape($value);
			echo "
			<td><a href=\"".BASE_URL."/tickets/viewTicket.php?ticket_id=$row[_id]\">
					$value
				</a>
			</td>
			";
		}
	}
	echo '</tr>';
}
echo "</tbody>";