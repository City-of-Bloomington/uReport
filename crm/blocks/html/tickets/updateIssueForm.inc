<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 * @param Issue $this->issue
 * @param int $this->index
 */
$title = is_null($this->index) ? 'Add Issue' : 'Edit Issue';
?>
<div class="updateIssueForm">
	<h2><?php echo $title; ?></h2>
	<form method="post" action="<?php echo $_SERVER['REDIRECT_URL']; ?>">
		<fieldset><legend>Issue Info</legend>
			<input name="ticket_id" type="hidden" value="<?php echo $this->ticket->getId(); ?>" />
			<input name="index" type="hidden" value="<?php echo $this->index; ?>" />
			<?php
				include APPLICATION_HOME.'/blocks/html/tickets/issueFields.inc';
				include APPLICATION_HOME.'/blocks/html/tickets/issueCustomFields.inc';
			?>
			<button type="submit" class="submit">Submit</button>
			<button type="button" class="cancel"
				onclick="document.location.href='<?php echo $this->ticket->getURL(); ?>';">
				Cancel
			</button>
		</fieldset>
	</form>
</div>