<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$title = $this->resolution->getId() ? 'Edit Resolution' : 'Add Resolution';
$name = View::escape($this->resolution->getName());
$description = View::escape($this->resolution->getDescription());
?>
<h1><?php echo $title; ?></h1>
<form method="post" action="<?php echo BASE_URL; ?>/resolutions/update">
	<fieldset><legend>Resolution Info</legend>
		<input name="resolution_id" type="hidden" value="<?php echo "{$this->resolution->getId()}"; ?>" />
		<table>

			<tr><td><label for="name" class="required">Name</label></td>
				<td><input name="name" id="name" value="<?php echo $name; ?>" /></td>
			</tr>

			<tr><td><label for="description">Description</label></td>
				<td><input name="description" id="description" value="<?php echo $description; ?>" /></td>
			</tr>

		</table>
		<button type="submit" class="submit">Submit</button>
		<button type="button" class="cancel"
			onclick="document.location.href='<?php echo BASE_URL; ?>/resolutions';">
			Cancel
		</button>
	</fieldset>
</form>