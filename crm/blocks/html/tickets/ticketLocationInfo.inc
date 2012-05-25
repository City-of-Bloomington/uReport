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
		$locationFields = array('city','state','zip','latitude','longitude');
		foreach ($locationFields as $field) {
			$get = 'get'.ucfirst($field);
			$$field = View::escape($this->ticket->$get());
			if ($$field) {
				echo "<tr><th>$field</th><td>{$$field}</td></tr>";
			}
		}
		$additionalFields = $this->ticket->getAdditionalFields();
		if ($additionalFields) {
			foreach ($additionalFields as $key=>$value) {
				echo "<tr><th>$key</th><td>$value</td></tr>";
			}
		}
	?>
	</table>
	<?php
		if ($latitude && $longitude) {
			echo "
			<div id=\"location_map\"></div>
			<script type=\"text/javascript\">
				var DEFAULT_LATITUDE  = ".DEFAULT_LATITUDE.";
				var DEFAULT_LONGITUDE = ".DEFAULT_LONGITUDE.";
				var points = [{latitude:$latitude, longitude:$longitude}];
			</script>
			";
			$this->template->addToAsset('scripts', YUI.'/yui/yui-min.js');
			$this->template->addToAsset('scripts', GOOGLE_MAPS);
			$this->template->addToAsset('scripts', BASE_URL.'/js/tickets/map.js');
		}
	?>
</div>
