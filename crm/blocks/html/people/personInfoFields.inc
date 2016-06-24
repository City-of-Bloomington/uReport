<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
use Blossom\Classes\View;

$fields = array(
	'firstname','middlename','lastname','organization'
);
foreach ($fields as $field) {
	$get = 'get'.ucfirst($field);
	$$field = self::escape($this->person->$get());
}
?>
<fieldset>
	<legend>
	<?php
		echo $this->_(array('labels.person', 'labels.people', 1)).' '.$this->_('labels.info');
	?>
	</legend>
	<input name="person_id" type="hidden" value="<?= $this->person->getId(); ?>" />
	<?php
		// Preserve any extra parameters passed in
		foreach ($_REQUEST as $key=>$value) {
			if (!in_array($key,$fields)) {
				$key   = self::escape($key);
				$value = self::escape($value);
				echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />\n";
			}
		}
	?>
	<table>
		<tr><td><label for="firstname" class="required">
					<?= $this->_('labels.firstname'); ?>
					<span class="fa fa-asterisk"></span>
				</label>
			</td>
			<td><input name="firstname" id="firstname" value="<?= $firstname; ?>" /></td>
		</tr>

		<tr><td><label for="middlename"><?= $this->_('labels.middlename'); ?></label></td>
			<td><input name="middlename" id="middlename" value="<?= $middlename; ?>" /></td>
		</tr>

		<tr><td><label for="lastname" class="required">
					<?= $this->_('labels.lastname'); ?>
					<span class="fa fa-asterisk"></span>
				</label>
			</td>
			<td><input name="lastname" id="lastname" value="<?= $lastname; ?>" /></td>
		</tr>
		<?php
			// Email and Phone are only valid for new records
			if (!$this->person->getId()) {
				$email = !empty($_POST['email']) ? self::escape($_POST['email']) : '';
				$phone = !empty($_POST['phone']) ? self::escape($_POST['phone']) : '';
				echo "
				<tr><td><label for=\"email\">{$this->_(array('labels.email', 'labels.emails', 1))}</label></td>
					<td><input name=\"email\" id=\"email\" value=\"$email\" /></td>
				</tr>
				<tr><td><label for=\"phone\">{$this->_(array('labels.phone', 'labels.phones', 1))}</label></td>
					<td><input name=\"phone\" id=\"phone\" value=\"$phone\" /></td>
				</tr>
				";
			}
		?>
		<tr><td><label for="organization"><?= $this->_('labels.organization'); ?></label></td>
			<td><input name="organization" id="organization" value="<?= $organization; ?>" /></td>
		</tr>
	</table>
</fieldset>
