<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$title = $this->action->getId()
	? $this->translate('labels.edit').' '.$this->translate(array('labels.action', 'labels.actions', 1))
	: $this->translate('labels.add' ).' '.$this->translate(array('labels.action', 'labels.actions', 1));

$name = View::escape($this->action->getName());
$description = View::escape($this->action->getDescription());
?>
<div class="updateActionForm">
	<h2><?php echo $title; ?></h2>
	<p><?php echo $this->translate('text.actions_description'); ?></p>
	<table>
		<tr><th>{enteredByPerson}</th>
			<td><?php echo $this->translate('text.actions_enteredByPerson'); ?></td>
		</tr>
		<tr><th>{actionPerson}</th>
			<td><?php echo $this->translate('text.actions_actionPerson'); ?></td>
		</tr>
	</table>
	<form method="post" action="<?php echo BASE_URL; ?>/actions/update">
		<fieldset><legend>Action</legend>
			<input name="action_id" type="hidden" value="<?php echo $this->action->getId(); ?>" />
			<table>

				<tr><td><label for="name" class="required">
							<?php echo $this->translate('labels.name'); ?>
							<span class="icon-asterisk"></span>
						</label>
					</td>
					<td><input name="name" id="name" value="<?php echo $name; ?>" /></td>
				</tr>

				<tr><td><label for="description" class="required">
							<?php echo $this->translate('labels.description'); ?>
							<span class="icon-asterisk"></span>
						</label>
					</td>
					<td><input name="description" id="description" value="<?php echo $description; ?>" /></td>
				</tr>
			</table>
			<?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/actions');
			?>
		</fieldset>
	</form>
</div>
