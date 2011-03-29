<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Array $this->results
 * @param string $this->return_url (optional)
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
$url = isset($this->return_url)
	? new URL($this->return_url)
	: new URL(BASE_URL.'/locations/viewLocation.php');
foreach ($this->results as $location=>$street_address_id) {
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
if (isset($_GET['location']['text']) && $this->return_url) {
	$url->location = $_GET['location']['text'];
	$location = View::escape($_GET['location']['text']);
	echo "<tr><td><a href=\"$url\">$location</a></td><td></td></tr>";
}
echo "
	</tbody>
</table>

<table>
	<tr class=\"verified\">
		<td>The location is verified with Master Address.</td>
	</tr>
</table>
</div>
";
