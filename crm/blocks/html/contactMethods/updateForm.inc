<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Label $this->contactMethod
 */
$name = View::escape($this->contactMethod->getName());
?>
<div>
	<h2><?php echo $this->contactMethod->getId() ? 'Edit Contact Method' : 'Add Contact Method'; ?></h2>
	<form method="post" action="<?php echo BASE_URI; ?>/contactMethods/update">
		<input type="hidden" name="contactMethod_id" value="<?php echo $this->contactMethod->getId(); ?>" />

		<fieldset>
			<table>
				<tr><td><label for="name">Name</label></td>
					<td><input name="name" id="name" value="<?php echo $name; ?>" /></td>
				</tr>
			</table>

			<button type="submit" class="submit">Submit</button>
			<a class="cancel" href="<?php echo BASE_URI; ?>/contactMethods">Cancel</a>
		</fieldset>
	</form>
</div>