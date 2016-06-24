<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Substatus $this->substatus
 */
use Blossom\Classes\View;

$title = $this->substatus->getId() ? 'Edit Substatus' : 'Add Substatus';
$name        = self::escape($this->substatus->getName());
$description = self::escape($this->substatus->getDescription());
?>
<h1><?= $title; ?></h1>
<form method="post" action="<?= BASE_URL; ?>/substatus/update">
	<fieldset><legend><?= $this->_(array('labels.substatus', 'labels.substatuses', 1)); ?></legend>
		<input name="substatus_id" type="hidden" value="<?= "{$this->substatus->getId()}"; ?>" />
		<table>
			<tr><td><label for="name" class="required">
						<?= $this->_(array('labels.substatus', 'labels.substatuses', 1)); ?>
						<span class="fa fa-asterisk"></span>
					</label>
				</td>
				<td><select id="status" name="status">
					<?php
						foreach (array('open', 'closed') as $status) {
							$selected = $this->substatus->getStatus()==$status
								? 'selected="selected"'
								: '';
							echo "<option $selected>$status</option>";
						}
					?>
					</select>
				</td>
			</tr>

			<tr><td><label for="name" class="required"><?= $this->_('labels.name'); ?> <span class="fa fa-asterisk"></span></label></td>
				<td><input name="name" id="name" value="<?= $name; ?>" /></td>
			</tr>

			<tr><td><label for="description" class="required"><?= $this->_('labels.description'); ?></label></td>
				<td><input name="description" id="description" value="<?= $description; ?>" /></td>
			</tr>

			<tr><td><label for="default"><?= $this->_('labels.default'); ?></label></td>
				<td><input name="default" id="default" type="checkbox"
						<?= $this->substatus->isDefault() ? 'checked="checked"' : ''; ?> />
				</td>
			</tr>

		</table>
		<?php
			$helper = $this->template->getHelper('saveAndCancelButtons');
			echo $helper->saveAndCancelButtons(BASE_URI.'/substatus');
		?>
	</fieldset>
</form>
