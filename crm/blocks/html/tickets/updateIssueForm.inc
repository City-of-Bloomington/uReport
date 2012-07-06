<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 * @param Issue  $this->issue
 */
$title = is_null($this->issue->getId()) ? 'Add Issue' : 'Edit Issue';
?>
<div class="updateIssueForm">
	<h2><?php echo $title; ?></h2>
	<form method="post" action="<?php echo $_SERVER['REDIRECT_URL']; ?>">
		<fieldset><legend>Issue Info</legend>
			<input name="ticket_id" type="hidden" value="<?php echo $this->ticket->getId(); ?>" />
			<input name="issue_id" type="hidden" value="<?php echo $this->issue->getId(); ?>" />
			<?php
				include APPLICATION_HOME.'/blocks/html/tickets/issueFields.inc';
				include APPLICATION_HOME.'/blocks/html/tickets/issueCustomFields.inc';
			?>
			<button type="submit" class="submit">Submit</button>
			<a class="cancel" href="<?php echo $this->ticket->getURL(); ?>">Cancel</a>
		</fieldset>
	</form>
</div>