<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Address $this->address
 */
use Application\Models\Address;
use Blossom\Classes\View;

$fields = array(
	'address','city','state','zip'
);
foreach ($fields as $field) {
	$get = 'get'.ucfirst($field);
	$$field = self::escape($this->address->$get());
}
?>
<div class="updateAddressForm">
	<form method="post">
		<fieldset><legend><?= $this->_(array('address', 'addresses', 1)); ?></legend>
			<table>
				<tr><td><label for="label"><?= $this->_(array('label', 'labels', 1)); ?></label></td>
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
				<tr><td><label for="address"><?= $this->_(array('address', 'addresses', 1)); ?></label></td>
					<td><input name="address" id="address" value="<?= $address; ?>" /></td>
				</tr>

				<tr><td><label for="city"><?= $this->_('city'); ?></label></td>
					<td><input name="city" id="city" value="<?= $city; ?>" /></td>
				</tr>

				<tr><td><label for="state"><?= $this->_('state'); ?></label></td>
					<td><input name="state" id="state" size="2" maxlength="2" value="<?= $state; ?>" />
					</td>
				</tr>

				<tr><td><label for="zip"><?= $this->_('zip'); ?></label></td>
					<td><input name="zip" id="zip" size="5" maxlength="5" value="<?= $zip; ?>" />
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
				$cancelUrl = $this->template->controller == 'AccountController'
					? BASE_URI.'/account'
					: $this->address->getPerson()->getUrl();

				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons($cancelUrl);
			?>
		</fieldset>
	</form>
</div>
