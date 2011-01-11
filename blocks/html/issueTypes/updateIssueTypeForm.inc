<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$title = $this->issueType->getId() ? 'Edit Issue Type' : 'Add Issue Type';
?>
<h1><?php echo $title; ?></h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
	<fieldset><legend>IssueType Info</legend>
		<input name="id" type="hidden" value="<?php echo $this->issueType->getId(); ?>" />
		<table>

			<tr><td><label for="name" class="required">Name</label></td>
				<td><input name="name" id="name"
						value="<?php echo View::escape($this->issueType->getName()); ?>" />
				</td>
			</tr>

			<tr><td><label for="department_id">department_id</label></td>
				<td><input name="department_id" id="department_id" value="<?php echo $this->issueType->getDepartment_id(); ?>" />
				</td>
			</tr>

		</table>
		<?php
			echo $this->template->formButton('Submit','submit','submit');
			echo $this->template->formButton(
				'Cancel','button','cancel',null,"document.location.href='".BASE_URL."/issueTypes';"
			);
		?>
	</fieldset>
</form>