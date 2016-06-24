<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Action $this->action
 */
$title = $this->action->getId()
	? $this->_('edit').' '.$this->_('action')
	: $this->_('add' ).' '.$this->_('action');

$name        = self::escape($this->action->getName());
$description = self::escape($this->action->getDescription());
?>
<div class="updateActionForm">
	<h2><?= $title; ?></h2>
	<p><?= $this->_('text.actions_description'); ?></p>
	<table>
		<tr><th>{enteredByPerson}</th>
			<td><?= $this->_('text.actions_enteredByPerson'); ?></td>
		</tr>
		<tr><th>{actionPerson}</th>
			<td><?= $this->_('text.actions_actionPerson'); ?></td>
		</tr>
	</table>
	<form method="post" action="<?= BASE_URI; ?>/actions/update">
		<fieldset><legend><?= $this->_('action'); ?></legend>
			<input name="action_id" type="hidden" value="<?= $this->action->getId(); ?>" />
			<table>

				<tr><td><label for="name" class="required">
							<?= $this->_('name'); ?>
							<span class="fa fa-asterisk"></span>
						</label>
					</td>
					<td>
					<?php
                        echo $this->action->getType() === 'system'
                            ? $name
                            : "<input name=\"name\" id=\"name\" value=\"$name\" />";
					?>
					</td>
				</tr>

				<tr><td><label for="description" class="required">
							<?= $this->_('description'); ?>
							<span class="fa fa-asterisk"></span>
						</label>
					</td>
					<td><input name="description" id="description" value="<?= $description; ?>" /></td>
				</tr>
			</table>
			<?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/actions');
			?>
		</fieldset>
	</form>
</div>
