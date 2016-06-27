<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param string $this->title
 */
?>
<div class="findLocationForm">
	<h2><?= $this->title ? self::escape($this->title) : $this->_('find_location'); ?></h2>
	<?php
		if (defined('ADDRESS_SERVICE')) {
			$this->_include('locations/findAddressForm.inc');
			$this->_include('locations/findStreetForm.inc');
		}

		// Don't allow searching for descriptive locations if they're in
		// the process of adding a ticket
		// We don't want them to be able to add new stuff with locations
		// that are just random text
		//
		// (2013-02-13) However, if they do not have a master address system
		// this is the only way for employees to enter locations, so we need
		// to make sure it's there if they need it.
		if (!defined('ADDRESS_SERVICE') || isset($_REQUEST['return_url'])) {
			$this->_include('locations/findDescriptiveLocationForm.inc');
		}
		if (isset($_REQUEST['callback'])) {
			echo "
			<div>
				<button type=\"button\" onclick=\"self.close();\">
					<span class=\"icon-remove\"></span>
					{$this->_('cancel')}
				</button>
			</div>
			";
		}
	?>
</div>
