<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 */
use Blossom\Classes\View;
?>
<div class="ticketLocationInfo">
	<?php
		$list = "
		<div class=\"additionalLocationInfo\">
			<h2>{$this->_('info_location_additional')}</h2>
			<ul class=\"twoColumnlist\">
		";
		$locationFields = array('city','state','zip','latitude','longitude');
		foreach ($locationFields as $field) {
			$get = 'get'.ucfirst($field);
			$$field = self::escape($this->ticket->$get());
			if ($$field) {
				$list.= "<li><label>$field: </label>{$$field}</li>";
			}
		}
		$additionalFields = $this->ticket->getAdditionalFields();
		if ($additionalFields) {
			foreach ($additionalFields as $key=>$value) {
				$list.= "<li><label>$key:</label> $value</li>";
			}
		}
		$list.= '</ul></div>';

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
