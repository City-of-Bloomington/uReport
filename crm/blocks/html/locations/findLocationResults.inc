<div class="findLocationResults">
<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Array $this->results
 */
$callback = isset($_REQUEST['callback'])
	? preg_replace('/[^a-zA-Z_\.]/', '', $_REQUEST['callback'])
	: '';

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
		if ($callback) {
			$url = "javascript:self.opener.$callback('$location');";
		}
		else {
			$url->location = $location;
		}

		$location = View::escape($location);
		$city = isset($data['city']) ? ", $data[city]" : '';
		$class = $data['addressId'] ? 'class="verified"' : '';
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

	// If they don't have a master address service they still need a way to add
	// new locations.  However, once they have a master address system, users
	// should not be able to add new addresses.  Instead, they should only
	// choose addresses returned from the search results
	if (!defined('ADDRESS_SERVICE')
		&& (isset($_REQUEST['return_url']) || $callback)) {
		$return_url = isset($_REQUEST['return_url']) ? $_REQUEST['return_url'] : '';
		$onsubmit = $callback
			? "self.opener.$callback(document.getElementById('location').value); return false;"
			: '';
		echo "
		<form method=\"get\" action=\"$return_url\" onsubmit=\"$onsubmit\">
			<fieldset><legend>Add a location</legend>
				<table>
					<tr><th><label for=\"location\">Location</th>
						<td><input id=\"location\" name=\"location\" /></td>
					</tr>
				</table>
				<button type=\"submit\" class=\"submit\">Submit</button>
			</fieldset>
		</form>
		";
	}
}
?>
</div>