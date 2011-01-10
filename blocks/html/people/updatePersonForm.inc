<?php
/**
 * @copyright 2009-2010 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
?>
<h1>Update Person</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
	<fieldset><legend>Person Info</legend>
		<input name="person_id" type="hidden" value="<?php echo $this->person->getId(); ?>" />

		<table>
			<tr><td><label for="person-firstname" class="required">Firstname</label></td>
				<td><input name="person[firstname]" id="person-firstname"
						value="<?php echo View::escape($this->person->getFirstname()); ?>" />
				</td>
			</tr>

			<tr><td><label for="person-lastname" class="required">Lastname</label></td>
				<td><input name="person[lastname]" id="person-lastname"
						value="<?php echo View::escape($this->person->getLastname()); ?>" />
				</td>
			</tr>

			<tr><td><label for="person-email">Email</label></td>
				<td><input name="person[email]" id="person-email"
						value="<?php echo View::escape($this->person->getEmail()); ?>" />
				</td>
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
