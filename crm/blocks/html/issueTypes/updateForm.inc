<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param IssueType $this->issueType
 */
$name = View::escape($this->issueType->getName());
?>
<div>
	<h2><?php echo $this->issueType->getId() ? 'Edit' : 'Add'; ?> Issue Type</h2>
	<form method="post" action="<?php echo BASE_URI; ?>/issueTypes/update">
		<input type="hidden" name="issueType_id" value="<?php echo $this->issueType->getId(); ?>" />

		<fieldset>
			<table>
				<tr><td><label for="name">Name</label></td>
					<td><input name="name" id="name" value="<?php echo $name; ?>" /></td>
				</tr>
			</table>

			<button type="submit" class="submit">Submit</button>
			<button type="button" class="cancel"
				onclick="document.location.href='<?php echo BASE_URI; ?>/issueTypes';">
				Cancel
			</button>
		</fieldset>
	</form>
</div>