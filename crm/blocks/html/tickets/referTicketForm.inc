<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>, W. Sibo <sibow@bloomington.in.gov>
 * @param Ticket $this->ticket
 * @param Person $this->person
 */
?>
<div class="referTicketForm">
	<form method="post" action="<?php echo $_SERVER['REDIRECT_URL']; ?>">
		<fieldset><legend><?php echo $this->translate('labels.refer_ticket'); ?></legend>
			<input name="ticket_id" type="hidden" value="<?php echo $this->ticket->getId(); ?>" />
			<?php
				// Preserve any extra parameters passed in
				foreach ($_REQUEST as $key=>$value) {
					if ($key!='ticket_id' && $key!='return_url') {
						$key = View::escape($key);
						$value = View::escape($value);
						echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />\n";
					}
				}
			?>
			<table>
				<tr><td><label for="referredPerson_id">
						<?php echo $this->translate(array('labels.person', 'labels.people', 1)); ?>
						</label>
					</td>
					<td><?php echo View::escape($this->person->getFullname()); ?>
						<input name="referredPerson_id" id="referredPerson_id" type="hidden"
							value="<?php echo $this->person->getId(); ?>" />
					</td>
				</tr>
			</table>
			<div><label for="notes"><?php echo $this->translate(array('labels.comment', 'labels.comments', 2)); ?></label></div>
			<div><textarea name="notes" id="notes"></textarea></div>
			<?php
				include APPLICATION_HOME.'/blocks/html/tickets/partials/submitAndCancelButtons.inc';
			?>
		</fieldset>
	</form>
</div>
