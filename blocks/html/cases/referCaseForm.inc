<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>, W. Sibo <sibow@bloomington.in.gov>
 * @param Case $this->case
 * @param Person $this->person
 */
?>
<div class="referCaseForm">
	<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
		<fieldset><legend>Refer Case</legend>
			<input name="case_id" type="hidden" value="<?php echo $this->case->getId(); ?>" />
			<table>
				<tr><td><label for="referredPerson">Person</label></td>
					<td><?php echo View::escape($this->person->getFullname()); ?>
						<input name="referredPerson" id="referredPerson" type="hidden"
							value="<?php echo $this->person->getId(); ?>" />
					</td>
				</tr>
			</table>
			<div><label for="notes">Notes</label></div>
			<div><textarea name="notes" id="notes" rows="3" cols="50"></textarea></div>
			<?php
				echo $this->template->formButton('Submit','submit','submit');
				echo $this->template->formButton(
					'Cancel','button','cancel',null,"document.location.href='{$this->case->getURL()}';"
				);
			?>
		</fieldset>
	</form>
</div>
