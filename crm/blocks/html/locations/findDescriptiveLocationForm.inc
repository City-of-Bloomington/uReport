<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
$text = isset($_GET['location']['text']) ? self::escape($_GET['location']['text']) : '';
?>
<div class="findDescriptiveLocationForm">
	<form method="get" action="<?= BASE_URL; ?>/locations">
		<fieldset><legend><?= $this->_('find_location'); ?></legend>
			<input name="location[text]" id="location-text" value="<?= $text; ?>" />
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
			<button type="submit" class="search"><?= $this->_('search'); ?></button>
		</fieldset>
	</form>
</div>
