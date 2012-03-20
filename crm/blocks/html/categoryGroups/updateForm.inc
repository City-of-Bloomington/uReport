<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param CategoryGroup $this->categoryGroup
 */
$title = $this->categoryGroup->getId() ? 'Edit Group' : 'Add Group';
$name = View::escape($this->categoryGroup->getName());
?>
<div class="updateCategoryGroupForm">
	<h2><?php echo $title; ?></h2>
	<form method="post" action="<?php echo BASE_URI; ?>/categoryGroups/update">
		<fieldset>
			<input name="categoryGroup_id" type="hidden" value="<?php echo $this->categoryGroup->getId(); ?>" />
			<table>
				<tr><td><label for="name">Name</label></td>
					<td><input name="name" id="name" value="<?php echo $name; ?>" /></td>
				</tr>
			</table>

			<button type="submit" class="submit">Submit</button>
			<a href="<?php echo BASE_URI; ?>/categoryGroups">Cancel</a>
		</fieldset>
	</form>
</div>