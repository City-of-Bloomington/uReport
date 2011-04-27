<?php
/**
 * Displays a list of Tickets
 *
 * This page only displays Ticket and Issue fields that are asked for
 * The fields desired need to be the indexes of an associative array
 * The values for each field just needs to be truthy
 *
 * $exampleFields = array('ticket-id'=>1,'ticket-status'=>1,'issue-issueType'=>1);
 *
 * For the full list of possible fields for use here,
 * See: TicketList::getDisplayableFields()
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TicketList $this->ticketList
 * @param array $this->fields The ticket fields to display.
 */
// Default fields to be used
if (!$this->fields) {
	$this->fields = array(
		'ticket-id'=>1,'ticket-status'=>1,
		'ticket-location'=>1,'ticket-enteredDate'=>1,'ticket-enteredByPerson'=>1
	);
}
$title = $this->title ? View::escape($this->title) : 'Tickets';
echo "
<div class=\"searchResults\">
	<h1>$title</h1>
";
	if (count($this->ticketList)) {
		echo "
		<table>
			<thead>
				<tr>
		";
				$fields = TicketList::getDisplayableFields();
				foreach ($this->fields as $name=>$value) {
					echo "<th>$fields[$name]</th>";
				}
		echo "
					<th></th>
				</tr>
			</thead>
			<tbody>
		";
			foreach ($this->ticketList as $ticket) {
				echo '<tr>';
				foreach ($this->fields as $field=>$value) {
					$get = 'get'.ucfirst($field);
					$value = $ticket->$get();
					if (preg_match('/Person/',$field) || $field=='constituent') {
						if ($value) {
							$value = $value['fullname'];
						}
					}
					if (false !== strpos($field,'Date')) {
						$value = date(DATE_FORMAT,$value->sec);
					}
					if ($field=='categories') {
						$c = array();
						foreach ($value as $category) {
							$c[] = $category['name'];
						}
						$value = implode(',',$c);
					}
					$value = View::escape($value);
					echo "<td><a href=\"{$ticket->getURL()}\">$value</a></td>";
				}
				echo '</tr>';
			}
		echo "
			</tbody>
		</table>
		";
	}
	else {
		echo "<h2>No tickets found</h2>";
	}
echo "
</div>
";
