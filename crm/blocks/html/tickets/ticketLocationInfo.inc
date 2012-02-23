<?php
/**
* @copyright 2011-2012 City of Bloomington, Indiana
* @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
* @author Cliff Ingham <inghamn@bloomington.in.gov>
* @param Ticket $this->ticket
*/
?>
<div class="ticketLocationInfo">
	<h2>Additional Location Information</h2>
	<table>
	<?php
		$additionalFields = array('city','state','zip','latitude','longitude');
		foreach ($additionalFields as $field) {
			$get = 'get'.ucfirst($field);
			$value = View::escape($this->ticket->$get());
			if ($value) {
				echo "<tr><th>$field</th><td>$value</td></tr>";
			}
		}
		foreach (array_keys(AddressService::$customFieldDescriptions) as $field) {
			$value = View::escape($this->ticket->get($field));
			if ($value) {
				echo "<tr><th>$field</th><td>$value</td></tr>";
			}
		}
	?>
	</table>
</div>
