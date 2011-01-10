<?php
/**
 * @copyright 2009-2010 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<h1>Add Person</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
	<fieldset><legend>Personal Info</legend>
		<table>

			<tr><td><label for="person-firstname" class="required">Firstname</label></td>
				<td><input name="person[firstname]" id="person-firstname" />
				</td>
			</tr>

			<tr><td><label for="person-lastname" class="required">Lastname</label></td>
				<td><input name="person[lastname]" id="person-lastname" />
				</td>
			</tr>

			<tr><td><label for="person-email">Email</label></td>
				<td><input name="person[email]" id="person-email" />
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
