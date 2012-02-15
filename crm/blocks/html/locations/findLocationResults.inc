<div class="findLocationResults">
<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Array $this->results
 */
if (count($this->results)) {
	echo "
	<table>
		<thead>
			<tr><th>Location</th>
				<th>Cases</th>
			</tr>
		</thead>
		<tbody>
	";

	$url = !empty($_REQUEST['return_url'])
		? new URL($_REQUEST['return_url'])
		: new URL(BASE_URL.'/locations/view');

	foreach ($this->results as $location=>$data) {
		if (isset($_REQUEST['callback'])) {
			$callback = preg_replace('/[^a-zA-Z_\.]/', '', $_REQUEST['callback']);
			$url = "javascript:self.opener.$callback('$location');";
		}
		else {
			$url->location = $location;
		}

		$location = View::escape($location);
		$city = isset($data['city']) ? ", $data[city]" : '';
		$class = $data['address_id'] ? 'class="verified"' : '';
		$count = isset($data['ticketCount']) ? $data['ticketCount'] : '';
		echo "
		<tr $class>
			<td><a href=\"$url\">$location$city</a></td>
			<td>$count</td>
		</tr>
		";
	}

	echo "
		</tbody>
	</table>

	<table>
		<tr class=\"verified\">
			<td>indicates verified locations</td>
		</tr>
	</table>
	";
}
else {
	echo '<p>No locations found. Please try your search again or contact an administrator if you believe a location is missing from our database.</p>';
}
?>
</div>