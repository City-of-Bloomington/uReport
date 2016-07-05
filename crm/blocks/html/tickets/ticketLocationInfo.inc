<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Ticket $this->ticket
 */
?>
<div class="ticketLocationInfo">
	<?php
        $list = '';
		$locationFields = ['city', 'state', 'zip', 'latitude', 'longitude'];
		foreach ($locationFields as $field) {
			$get = 'get'.ucfirst($field);
			$$field = self::escape($this->ticket->$get());
			if ($$field) {
				$list.= "<dl class=\"$field\"><dt>{$this->_($field)}:</dt><dd>{$$field}</dd></dl>";
			}
		}

		if ($latitude && $longitude) {
			echo "
			<div id=\"location_map\"></div>
			<script type=\"text/javascript\">
				var DEFAULT_LATITUDE  = ".DEFAULT_LATITUDE.";
				var DEFAULT_LONGITUDE = ".DEFAULT_LONGITUDE.";
				var points = [{latitude:$latitude, longitude:$longitude}];
			</script>
			";
			$this->template->addToAsset('scripts', GOOGLE_MAPS);
			$this->template->addToAsset('scripts', BASE_URL.'/js/tickets/map.js');
		}
		echo $list;
	?>
</div>
