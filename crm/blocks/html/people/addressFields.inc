<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
$fields = array(
	'address','city','state','zip'
);
foreach ($fields as $field) {
	$get = 'get'.ucfirst($field);
	$$field = View::escape($this->person->$get());
}
?>
<fieldset><legend>Address</legend>
	<table>
		<tr><td><label for="address">Address</label></td>
			<td><input name="address" id="address" value="<?php echo $address; ?>" /></td>
		</tr>

		<tr><td><label for="city">City</label></td>
			<td><input name="city" id="city" value="<?php echo $city; ?>" /></td>
		</tr>

		<tr><td><label for="state">State</label></td>
			<td><input name="state" id="state" size="2" maxlength="2" value="<?php echo $state; ?>" />
			</td>
		</tr>

		<tr><td><label for="zip">Zip</label></td>
			<td><input name="zip" id="zip" size="5" maxlength="5" value="<?php echo $zip; ?>" />
			</td>
		</tr>
	</table>
</fieldset>
