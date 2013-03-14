<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Substatus $this->substatus
 */
$title = $this->substatus->getId() ? 'Edit Substatus' : 'Add Substatus';
$name        = View::escape($this->substatus->getName());
$description = View::escape($this->substatus->getDescription());
?>
<h1><?php echo $title; ?></h1>
<form method="post" action="<?php echo BASE_URL; ?>/substatus/update">
	<fieldset><legend>Substatus Info</legend>
		<input name="substatus_id" type="hidden" value="<?php echo "{$this->substatus->getId()}"; ?>" />
		<table>
			<tr><td><label for="name" class="required">Status</label></td>
				<td><select id="status" name="status">
					<?php
						foreach (array('open', 'closed') as $status) {
							$selected = $this->substatus->getStatus()==$status
								? 'selected="selected"'
								: '';
							echo "<option $selected>$status</option>";
						}
					?>
					</select>
				</td>
			</tr>

			<tr><td><label for="name" class="required">Name</label></td>
				<td><input name="name" id="name" value="<?php echo $name; ?>" /></td>
			</tr>

			<tr><td><label for="description">Description</label></td>
				<td><input name="description" id="description" value="<?php echo $description; ?>" /></td>
			</tr>

		</table>
		<button type="submit" class="submit">Submit</button>
		<a class="cancel" href="<?php echo BASE_URL; ?>/substatus">Cancel</a>
	</fieldset>
</form>