<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>, W. Sibo <sibow@bloomington.in.gov>
 * @param Ticket $this->ticket
 * @param Person $this->person
 */
?>
<div class="referTicketForm">
	<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
		<fieldset><legend>Refer Case</legend>
			<input name="ticket_id" type="hidden" value="<?php echo $this->ticket->getId(); ?>" />
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
			<button type="submit" class="submit">Submit</button>
			<button type="button" class="cancel"
				onclick="document.location.href='<?php echo $this->ticket->getURL(); ?>';">
				Cancel
			</button>
		</fieldset>
	</form>
</div>
