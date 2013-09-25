<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
$fields = array(
	'firstname','middlename','lastname','organization'
);
foreach ($fields as $field) {
	$get = 'get'.ucfirst($field);
	$$field = View::escape($this->person->$get());
}
?>
<fieldset>
	<legend>
	<?php
		echo $this->translate(array('labels.person', 'labels.people', 1)).' '.$this->translate('labels.info');
	?>
	</legend>
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
		<tr><td><label for="firstname" class="required">
					<?php echo $this->translate('labels.firstname'); ?>
					<span class="icon-asterisk"></span>
				</label>
			</td>
			<td><input name="firstname" id="firstname" value="<?php echo $firstname; ?>" /></td>
		</tr>

		<tr><td><label for="middlename"><?php echo $this->translate('labels.middlename'); ?></label></td>
			<td><input name="middlename" id="middlename" value="<?php echo $middlename; ?>" /></td>
		</tr>

		<tr><td><label for="lastname" class="required">
					<?php echo $this->translate('labels.lastname'); ?>
					<span class="icon-asterisk"></span>
				</label>
			</td>
			<td><input name="lastname" id="lastname" value="<?php echo $lastname; ?>" /></td>
		</tr>
		<?php
			// Email and Phone are only valid for new records
			if (!$this->person->getId()) {
				$email = !empty($_POST['email']) ? View::escape($_POST['email']) : '';
				$phone = !empty($_POST['phone']) ? View::escape($_POST['phone']) : '';
				echo "
				<tr><td><label for=\"email\">{$this->translate(array('labels.email', 'labels.emails', 1))}</label></td>
					<td><input name=\"email\" id=\"email\" value=\"$email\" /></td>
				</tr>
				<tr><td><label for=\"phone\">{$this->translate(array('labels.phone', 'labels.phones', 1))}</label></td>
					<td><input name=\"phone\" id=\"phone\" value=\"$phone\" /></td>
				</tr>
				";
			}
		?>
		<tr><td><label for="organization"><?php echo $this->translate('labels.organization'); ?></label></td>
			<td><input name="organization" id="organization" value="<?php echo $organization; ?>" /></td>
		</tr>
	</table>
</fieldset>
