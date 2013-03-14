<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$title = $this->action->getId() ? 'Edit Action' : 'Add Action';

$name = View::escape($this->action->getName());
$description = View::escape($this->action->getDescription());
?>
<div class="updateActionForm">
	<h1><?php echo $title; ?></h1>
	<h2>Description variables</h2>
	<p>You can call for data by using these strings inside the description</p>
	<table>
		<tr><th>{enteredByPerson}</th>
			<td>The person who entered the action</td>
		</tr>
		<tr><th>{actionPerson}</th>
			<td>The person the action is about</td>
		</tr>
	</table>
	<form method="post" action="<?php echo BASE_URL; ?>/actions/update">
		<fieldset><legend>Action</legend>
			<input name="action_id" type="hidden" value="<?php echo $this->action->getId(); ?>" />
			<table>

				<tr><td><label for="name" class="required">Name</label></td>
					<td><input name="name" id="name" value="<?php echo $name; ?>" /></td>
				</tr>

				<tr><td><label for="description" class="required">Description</label></td>
					<td><input name="description" id="description" value="<?php echo $description; ?>" /></td>
				</tr>
			</table>
			<button type="submit" class="submit">Submit</button>
			<a class="cancel" href="<?php echo BASE_URL; ?>/actions">Cancel</a>
		</fieldset>
	</form>
</div>
