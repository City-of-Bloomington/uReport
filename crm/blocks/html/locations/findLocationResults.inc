<?php
/**
 * @copyright 2011-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Array $this->results
 */
use Blossom\Classes\Url;
use Blossom\Classes\View;
?>
<div class="findLocationResults">
<?php
$callback = isset($_REQUEST['callback'])
	? preg_replace('/[^a-zA-Z_\.]/', '', $_REQUEST['callback'])
	: '';

if (count($this->results)) {
	echo "
	<table>
		<thead>
			<tr><th>{$this->_(array('location', 'locations', 1))}</th>
				<th>{$this->_(array('ticket', 'tickets', 2))}</th>
			</tr>
		</thead>
		<tbody>
	";

	$url = !empty($_REQUEST['return_url'])
		? new Url($_REQUEST['return_url'])
		: new Url(BASE_URL.'/locations/view');

	foreach ($this->results as $location=>$data) {
		if ($callback) {
			$url = "javascript:self.opener.$callback('$location');";
		}
		else {
			$url->location = $location;
		}

		$location = self::escape($location);
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
			<td>{$this->_('messages.verified_locations')}</td>
		</tr>
	</table>
	";
}
else {
	echo "<p>{$this->_('messages.no_locations_found')}</p>";

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
			<fieldset>
				<legend>
					{$this->_('add')}
					{$this->_(array('location', 'locations', 1))}
				</legend>
				<table>
					<tr><th><label for=\"location\">{$this->_(array('location', 'locations', 1))}</th>
						<td><input id=\"location\" name=\"location\" /></td>
					</tr>
				</table>
				<button type=\"submit\"><span class=\"fa fa-plus\"></span> {$this->_('submit')}</button>
			</fieldset>
		</form>
		";
	}
}
?>
</div>
