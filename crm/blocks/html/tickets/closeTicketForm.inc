<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 */
?>
<div class="closeTicketForm">
	<form method="post" action="<?php echo BASE_URL; ?>/tickets/closeTicket.php">
		<fieldset><legend>Close This Case</legend>
			<input name="ticket_id" type="hidden" value="<?php echo $this->ticket->getId(); ?>" />
			<table>
				<tr><td><label for="resolution">Resolution</label></td>
					<td><select name="resolution" id="resolution">
						<?php
							$list = new ResolutionList();
							$list->find();
							foreach ($list as $resolution) {
								$name = View::escape($resolution->getName());
								echo "<option>$name</option>";
							}
						?>
						</select>
					</td>
				</tr>
				<tr><td><label id="notes">Comment</label></td>
					<td><textarea id="notes" name="notes" rows="3" cols="50"></textarea></td>
				</tr>
			</table>
			<button type="submit" class="submit">Submit</button>
			<button type="button" class="cancel"
				onclick="document.location.href='<?php echo $this->ticket->getURL(); ?>';">
				Cancel
			</button>
		</fieldset>
	</form>
</div>