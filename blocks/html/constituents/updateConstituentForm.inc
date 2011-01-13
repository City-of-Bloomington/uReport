<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param string $this->return_url Optional
 */
$fields = array('firstname','lastname','middlename','salutation','address','city','state','zip','email');
foreach ($fields as $field) {
	$getField = 'get'.ucfirst($field);
	$$field = View::escape($this->constituent->$getField());
}
$title = $this->constituent->getId() ? 'Edit Constituent' : 'Add Constituent';
?>
<h1><?php echo $title; ?></h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
	<fieldset><legend>Constituent Info</legend>
		<input name="constituent_id" type="hidden" value="<?php echo $this->constituent->getId(); ?>" />
		<table>

			<tr><td><label for="firstname" class="required">First Name</label></td>
				<td><input name="firstname" id="firstname" value="<?php echo $firstname; ?>" /></td>
			</tr>

			<tr><td><label for="lastname" class="required">Last Name</label></td>
				<td><input name="lastname" id="lastname" value="<?php echo $lastname; ?>" />
				</td>
			</tr>

			<tr><td><label for="middlename">Middle Name</label></td>
				<td><input name="middlename" id="middlename" value="<?php echo $middlename; ?>" />
				</td>
			</tr>

			<tr><td><label for="salutation">Salutation</label></td>
				<td><input name="salutation" id="salutation" value="<?php echo $salutation; ?>" />
				</td>
			</tr>

			<tr><td><label for="address">Address</label></td>
				<td><input name="address" id="address" value="<?php echo $address; ?>" />
				</td>
			</tr>

			<tr><td><label for="city">City</label></td>
				<td><input name="city" id="city" value="<?php echo $city; ?>" />
				</td>
			</tr>

			<tr><td><label for="state">State</label></td>
				<td><input name="state" id="state" value="<?php echo $state; ?>" />
				</td>
			</tr>

			<tr><td><label for="zip">Zip</label></td>
				<td><input name="zip" id="zip" value="<?php echo $zip; ?>" />
				</td>
			</tr>

			<tr><td><label for="email">email</label></td>
				<td><input name="email" id="email" value="<?php echo $this->constituent->getEmail(); ?>" />
				</td>
			</tr>

		</table>

		<input name="return_url" type="hidden" value="<?php echo $this->return_url; ?>" />
		<?php
			$return_url = $this->return_url ? $this->return_url : BASE_URL.'/constituents';

			echo $this->template->formButton('Submit','submit','submit');
			echo $this->template->formButton(
				'Cancel','button','cancel',null,"document.location.href='$return_url';"
			);
		?>
	</fieldset>
</form>