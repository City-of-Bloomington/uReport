<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TicketList $this->ticketList
 * @param array $this->fields The ticket fields to display.
 *							These will be indexes from TicketList::getDisplayableFields
 */
?>
<div class="searchResults">
<h1><?php echo $this->title ? View::escape($this->title) : 'Tickets'; ?></h1>
<table>
	<thead>
		<tr>
		<?php
			$fields = TicketList::getDisplayableFields();
			foreach ($this->fields as $name=>$value) {
				echo "<th>$fields[$name]</th>";
			}
		?>
		</tr>
	</thead>
	<tbody>
	<?php
		foreach ($this->ticketList as $ticket) {
			foreach ($ticket->getIssues() as $issue) {
				echo '<tr>';
				foreach ($this->fields as $name=>$value) {
					list($var,$field) = explode('-',$name);
					$get = 'get'.ucfirst($field);
					$value = $$var->$get();
					if (preg_match('/Person/',$name) || $field=='constituent') {
						if ($value) {
							$value = $value->getFullname();
						}
					}
					echo "<td>$value</td>";
				}
				echo '</tr>';
			}
		}
	?>
	</tbody>
</table>
</div>
