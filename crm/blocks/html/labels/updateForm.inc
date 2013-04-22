<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Label $this->label
 */
$name = View::escape($this->label->getName());
?>
<div>
	<h2><?php echo $this->label->getId() ? 'Edit Label' : 'Add Label'; ?></h2>
	<form method="post" action="<?php echo BASE_URI; ?>/labels/update">
		<input type="hidden" name="label_id" value="<?php echo $this->label->getId(); ?>" />

		<fieldset>
			<table>
				<tr><td><label for="name">Name</label></td>
					<td><input name="name" id="name" value="<?php echo $name; ?>" /></td>
				</tr>
			</table>

			<button type="submit"><span class="icon-save"></span> Save</button>
			<a class="btn" href="<?php echo BASE_URI; ?>/labels">
				<span class="icon-remove"></span>
				Cancel
			</a>
		</fieldset>
	</form>
</div>