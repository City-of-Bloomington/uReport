<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Phone $this->phone
 */
use Application\Models\Phone;
use Blossom\Classes\View;
?>
<fieldset><legend><?php echo $this->translate(array('labels.phone', 'labels.phones', 1)); ?></legend>
	<label>
		<?php echo $this->translate(array('labels.label', 'labels.label', 1)); ?>
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
	<input name="number" value="<?php echo View::escape($this->phone->getNumber()); ?>" />
</fieldset>
