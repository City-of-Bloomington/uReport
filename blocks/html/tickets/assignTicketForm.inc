<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>, W. Sibo <sibow@bloomington.in.gov>
 * @param Ticket $this->ticket
 */
?>
<div class="assignTicketForm">
	<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
		<fieldset><legend>Assign Ticket</legend>
			<input name="ticket_id" type="hidden" value="<?php echo $this->ticket->getId(); ?>" />
			<?php
				include APPLICATION_HOME.'/blocks/html/tickets/assignTicketFields.inc';

				echo $this->template->formButton('Submit','submit','submit');
				echo $this->template->formButton(
					'Cancel','button','cancel',null,"document.location.href='{$this->ticket->getURL()}';"
				);
			?>
		</fieldset>
	</form>
</div>
