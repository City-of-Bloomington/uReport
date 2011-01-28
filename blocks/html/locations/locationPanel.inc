<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param string $this->location
 * @param array $this->results
 */
?>
<div id="location-panel">
	<div class="left">
		<?php
			if ($this->location) {
				include APPLICATION_HOME.'/blocks/html/locations/locationInfo.inc';
			}
			else {
				include APPLICATION_HOME.'/blocks/html/locations/findLocationForm.inc';
			}
		?>
	</div>
	<div class="right">
		<?php
			if ($this->results) {
				include APPLICATION_HOME.'/blocks/html/locations/findLocationResults.inc';
			}
		?>
	</div>
</div>
