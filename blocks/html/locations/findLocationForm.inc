<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param string $this->title
 * @param string $this->return_url
 */
?>
<div class="findLocationForm">
	<h1><?php echo $this->title ? View::escape($this->title) : 'Find a Location'; ?></h1>
	<?php
		include APPLICATION_HOME.'/blocks/html/locations/findAddressForm.inc';
		include APPLICATION_HOME.'/blocks/html/locations/findStreetForm.inc';

		// Don't allow searching for descriptive locations if they're in
		// the process of adding a ticket
		// We don't want them to be able to add new stuff with locations
		// that are just random text
		if (!$this->return_url) {
			include APPLICATION_HOME.'/blocks/html/locations/findDescriptiveLocationForm.inc';
		}
	?>
</div>
