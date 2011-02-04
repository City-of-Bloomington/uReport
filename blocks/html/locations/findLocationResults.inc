<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Array $this->results
 */
echo "
<div class=\"findLocationResults\">
<table>
	<thead>
		<tr><th>Location</th>
			<th>Tickets</th>
		</tr>
	</thead>
	<tbody>
";
foreach ($this->results as $location=>$street_address_id) {
	$url = new URL(BASE_URL.'/locations/viewLocation.php');
	$url->location = $location;

	$tickets = new TicketList(array('location'=>$location));
	$c = count($tickets);
	$c = $c==0 ? '' : $c;

	$location = View::escape($location);
	$class = $street_address_id ? 'class="verified"' : '';
	echo "
	<tr $class>
		<td><a href=\"$url\">$location</a></td>
		<td>$c</td>
	</tr>
	";
}
echo "
	</tbody>
</table>

<table>
	<tr class=\"verified\">
		<td>The address is verified with Master Address as a registered address.
			Master Address will supply information about the address.
		</td>
	</tr>
</table>
</div>
";
