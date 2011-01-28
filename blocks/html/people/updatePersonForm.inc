<?php
/**
 * @copyright 2009-2010 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
$fields = array('firstname','middlename','lastname','email','address','phone');
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

			<tr><td><label for="address">Address</label></td>
				<td><input name="address" id="address" value="<?php echo $address; ?>" /></td>
			</tr>

			<tr><td><label for="phone">Phone</label></td>
				<td><input name="phone" id="phone" value="<?php echo $phone; ?>" /></td>
			</tr>
		</table>

		<?php
			echo $this->formButton('Submit','submit','submit');
			echo $this->formButton(
				'Cancel','button','cancel',null,"document.location.href='".BASE_URL."/people';"
			);
		?>
	</fieldset>
</form>
