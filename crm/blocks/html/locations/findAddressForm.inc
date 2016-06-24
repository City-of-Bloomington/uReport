<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Blossom\Classes\View;

$address = isset($_GET['location']['address']) ? self::escape($_GET['location']['address']) : '';
?>
<div class="findAddressForm">
	<form method="get" action="<?= BASE_URL; ?>/locations">
		<fieldset><legend><?= $this->_('labels.find_address'); ?></legend>
			<input name="location[address]" id="location-address" value="<?= $address; ?>" />
			<?php
				// Preserve any extra parameters passed in
				foreach ($_REQUEST as $key=>$value) {
					if ($key != 'location') {
						$key   = self::escape($key);
						$value = self::escape($value);
						echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />\n";
					}
				}
			?>
			<button type="submit">
				<span class="fa fa-search"></span>
				<?= $this->_('labels.search'); ?>
			</button>
		</fieldset>
	</form>
</div>
