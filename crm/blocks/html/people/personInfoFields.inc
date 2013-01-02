<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
$fields = array(
	'firstname','middlename','lastname','email','phoneNumber','organization'
);
foreach ($fields as $field) {
	$get = 'get'.ucfirst($field);
	$$field = View::escape($this->person->$get());
}
?>
<fieldset><legend>Person Info</legend>
	<input name="person_id" type="hidden" value="<?php echo $this->person->getId(); ?>" />
	<?php
		// Preserve any extra parameters passed in
		foreach ($_REQUEST as $key=>$value) {
			if (!in_array($key,$fields)) {
				$key = View::escape($key);
				$value = View::escape($value);
				echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />\n";
			}
		}
	?>
	<table>
		<tr><td><label for="firstname" class="required">Firstname</label></td>
			<td><input name="firstname" id="firstname" value="<?php echo $firstname; ?>" /></td>
		</tr>

		<tr><td><label for="middlename">Middlename</label></td>
			<td><input name="middlename" id="middlename" value="<?php echo $middlename; ?>" /></td>
		</tr>

		<tr><td><label for="lastname" class="required">Lastname</label></td>
			<td><input name="lastname" id="lastname" value="<?php echo $lastname; ?>" /></td>
		</tr>

		<tr><td><label for="email">Email</label></td>
			<td><input name="email" id="email" value="<?php echo $email; ?>" /></td>
		</tr>

		<tr><td><label for="phoneNumber">Phone</label></td>
			<td><input name="phoneNumber" id="phoneNumber" value="<?php echo $phoneNumber; ?>" /></td>
		</tr>

		<tr><td><label for="organization">Organization</label></td>
			<td><input name="organization" id="organization" value="<?php echo $organization; ?>" /></td>
		</tr>
	</table>
</fieldset>

