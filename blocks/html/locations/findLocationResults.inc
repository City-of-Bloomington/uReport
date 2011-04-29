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

	foreach ($this->results as $location=>$data) {
		$url->location = $location;

		$location = View::escape($location);
		$class = $data['address_id'] ? 'class="verified"' : '';
		$count = isset($data['ticketCount']) ? $data['ticketCount'] : '';
		echo "
		<tr $class>
			<td><a href=\"$url\">$location</a></td>
			<td>$count</td>
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
		<td>The location has been verified.</td>
	</tr>
</table>
</div>
";
