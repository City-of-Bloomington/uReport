<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$title = $this->actionType->getId() ? 'Edit Action Type' : 'Add Action Type';

$name = View::escape($this->actionType->getName());
$description = View::escape($this->actionType->getDescription());
$formLabel = View::escape($this->actionType->getFormLabel());
$status = View::escape($this->actionType->getStatus());
?>
<div class="updateActionTypeForm">
	<h1><?php echo $title; ?></h1>
	<h2>Description variables</h2>
	<p>You can call for data by using these strings inside the description</p>
	<table>
		<tr><th>{enteredBy}</th>
			<td>The person who entered the action</td>
		</tr>
		<tr><th>{actionPerson}</th>
			<td>The person the action is about</td>
		</tr>
	</table>
	<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
		<fieldset><legend>Action Type</legend>
			<input name="actionType_id" type="hidden" value="<?php echo $this->actionType->getId(); ?>" />
			<table>

				<tr><td><label for="name" class="required">Name</label></td>
					<td><input name="name" id="name" value="<?php echo $name; ?>" /></td>
				</tr>

				<tr><td><label for="description" class="required">Description</label></td>
					<td><input name="description" id="description" value="<?php echo $description; ?>" /></td>
				</tr>

				<tr><td><label for="formLabel" class="required">Form Label</label></td>
					<td><input name="formLabel" id="formLabel" value="<?php echo $formLabel; ?>" /></td>
				</tr>

				<tr><td><label for="status" class="required">Status</label></td>
					<td><input name="status" id="status" value="<?php echo $status; ?>" /></td>
			</table>
			<?php
				echo $this->template->formButton('Submit','submit','submit');
				echo $this->template->formButton(
					'Cancel','button','cancel',null,"document.location.href='".BASE_URL."/actions';"
				);
			?>
		</fieldset>
	</form>
</div>
