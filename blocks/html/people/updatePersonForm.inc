<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 * @param string $this->return_url
 */
$fields = array(
	'firstname','middlename','lastname','email','phone','organization',
	'address','city','state','zip'
);
foreach ($fields as $field) {
	$get = 'get'.ucfirst($field);
	$$field = View::escape($this->person->$get());
}
$title = $this->person->getId() ? View::escape('Edit '.$this->person->getFirstname()) : 'Add Someone';
echo "<h1>$title</h1>";
?>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
	<fieldset><legend>Person Info</legend>
		<input name="person_id" type="hidden" value="<?php echo $this->person->getId(); ?>" />

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

			<tr><td><label for="phone">Phone</label></td>
				<td><input name="phone" id="phone" value="<?php echo $phone; ?>" /></td>
			</tr>

			<tr><td><label for="organization">Organization</label></td>
				<td><input name="organization" id="organization" value="<?php echo $organization; ?>" /></td>
			</tr>
		</table>
	</fieldset>

	<fieldset><legend>Address</legend>
		<table>
			<tr><td><label for="address">Address</label></td>
				<td><input name="address" id="address" value="<?php echo $address; ?>" /></td>
			</tr>

			<tr><td><label for="city">City</label></td>
				<td><input name="city" id="city" value="<?php echo $city; ?>" /></td>
			</tr>

			<tr><td><label for="state">State</label></td>
				<td><input name="state" id="state" size="2" maxlength="2" />
					<label for="zip">Zip</label>
					<input name="zip" id="zip" size="5" maxlength="5" />
				</td>
			</tr>
		</table>
	</fieldset>

	<fieldset><legend>Submit</legend>
		<input name="return_url" type="hidden" value="<?php echo View::escape($this->return_url); ?>" />
		<?php
			echo $this->formButton('Submit','submit','submit');
			echo $this->formButton(
				'Cancel','button','cancel',null,"document.location.href='{$this->return_url}';"
			);
		?>
	</fieldset>
</form>
