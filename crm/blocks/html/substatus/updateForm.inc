<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Substatus $this->substatus
 */
$title = $this->substatus->getId() ? 'Edit Substatus' : 'Add Substatus';
$name        = View::escape($this->substatus->getName());
$description = View::escape($this->substatus->getDescription());
?>
<h1><?php echo $title; ?></h1>
<form method="post" action="<?php echo BASE_URL; ?>/substatus/update">
	<fieldset><legend><?php echo $this->translate(array('labels.substatus', 'labels.substatuses', 1)); ?></legend>
		<input name="substatus_id" type="hidden" value="<?php echo "{$this->substatus->getId()}"; ?>" />
		<table>
			<tr><td><label for="name" class="required">
						<?php echo $this->translate(array('labels.status', 'labels.statuses', 1)); ?>
						<span class="icon-asterisk"></span>
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

			<tr><td><label for="name" class="required"><?php echo $this->translate('labels.name'); ?> <span class="icon-asterisk"></span></label></td>
				<td><input name="name" id="name" value="<?php echo $name; ?>" /></td>
			</tr>

			<tr><td><label for="description" class="required"><?php echo $this->translate('labels.description'); ?></label></td>
				<td><input name="description" id="description" value="<?php echo $description; ?>" /></td>
			</tr>

			<tr><td><label for="default"><?php echo $this->translate('labels.default'); ?></label></td>
				<td><input name="default" id="default" type="checkbox"
						<?php echo $this->substatus->isDefault() ? 'checked="checked"' : ''; ?> />
				</td>
			</tr>

		</table>
		<?php
			$helper = $this->template->getHelper('saveAndCancelButtons');
			echo $helper->saveAndCancelButtons(BASE_URI.'/substatus');
		?>
	</fieldset>
</form>
