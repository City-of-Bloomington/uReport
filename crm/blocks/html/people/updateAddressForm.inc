<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Address $this->address
 */
$fields = array(
	'address','city','state','zip'
);
foreach ($fields as $field) {
	$get = 'get'.ucfirst($field);
	$$field = View::escape($this->address->$get());
}
?>
<div class="updateAddressForm">
	<form method="post" action="<?php echo BASE_URI; ?>/people/updateAddress">
		<fieldset><legend>Address</legend>
			<table>
				<tr><td><label for="label">Label</label></td>
					<td><select name="label" id="label">
						<?php
							foreach (Address::$LABELS as $label) {
								$selected = $this->address->getLabel()==$label
									? 'selected="selected"'
									: '';
								echo "<option $selected>$label</option>";
							}
						?>
						</select>
					</td>
				</tr>
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
		<fieldset>
			<?php
				if ($this->address->getId()) {
					echo "<input type=\"hidden\" name=\"address_id\" value=\"{$this->address->getId()}\" />";
				}
				else {
					echo "<input type=\"hidden\" name=\"person_id\" value=\"{$this->address->getPerson_id()}\" />";
				}
			?>
			<button type="submit" class="save">Save</button>
			<a class="cancel" href="<?php echo $this->address->getPerson()->getURL(); ?>">Cancel</a>
		</fieldset>
	</form>
</div>