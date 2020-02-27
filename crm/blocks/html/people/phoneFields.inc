<?php
/**
 * @copyright 2013-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Phone $this->phone
 */
use Application\Models\Phone;
?>
<fieldset><legend><?= $this->_('phone'); ?></legend>
	<label>
		<?= $this->_('label'); ?>
		<select name="label" id="label">
		<?php
			foreach (Phone::$LABELS as $label) {
				$selected = $this->phone->getLabel()== $label
					? 'selected="selected"'
					: '';
				echo "<option $selected>$label</option>";
			}
		?>
		</select>
	</label>
	<input name="number" value="<?= self::escape($this->phone->getNumber()); ?>" />
</fieldset>
