<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Department $this->department
 * @param array $this->action
 * @param int $this->index
 */
$title = $this->index ? 'Edit Action' : 'Add Action';
?>
<div class="updateActionForm">
	<h1><?php echo View::escape($this->department->getName()); ?></h1>
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
	<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
		<fieldset><legend>Action</legend>
			<input name="department_id" type="hidden" value="<?php echo $this->department->getId(); ?>" />
			<input name="index" type="hidden" value="<?php echo View::escape($this->index); ?>" />

			<table>

				<tr><td><label for="action-name" class="required">Name</label></td>
					<td><input name="action[name]" id="action-name"
							value="<?php echo isset($this->action['name']) ? View::escape($this->action['name']) : ''; ?>" />
					</td>
				</tr>

				<tr><td><label for="action-description" class="required">Description</label></td>
					<td><input name="action[description]" id="action-description"
							value="<?php echo isset($this->action['description']) ? View::escape($this->action['description']) : ''; ?>" />
					</td>
				</tr>

				<tr><td><label for="action-formLabel" class="required">Form Label</label></td>
					<td><input name="action[formLabel]" id="action-formLabel"
							value="<?php echo isset($this->action['formLabel']) ? View::escape($this->action['formLabel']) : ''; ?>" />
					</td>
				</tr>

			</table>
			<?php
				echo $this->template->formButton('Submit','submit','submit');

				$url = BASE_URL."/departments/actions.php?department_id={$this->department->getId()}";
				echo $this->template->formButton(
					'Cancel','button','cancel',null,
					"document.location.href='$url';"
				);
			?>
		</fieldset>
	</form>
</div>
