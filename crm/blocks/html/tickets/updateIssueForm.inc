<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 * @param Issue  $this->issue
 */
$title = is_null($this->issue->getId()) ? 'Add Issue' : 'Edit Issue';
?>
<div class="updateIssueForm">
	<h2><?= $title; ?></h2>
	<form method="post" action="<?= $_SERVER['REDIRECT_URL']; ?>">
		<fieldset><legend><?= $this->_('labels.info_issue'); ?></legend>
			<input name="ticket_id" type="hidden" value="<?= $this->ticket->getId(); ?>" />
			<input name="issue_id" type="hidden" value="<?= $this->issue->getId(); ?>" />
			<?php
				include APPLICATION_HOME.'/blocks/html/tickets/issueFields.inc';

				$this->category = $this->ticket->getCategory();
				include APPLICATION_HOME.'/blocks/html/tickets/issueCustomFields.inc';

				include APPLICATION_HOME.'/blocks/html/tickets/partials/submitAndCancelButtons.inc';
			?>
		</fieldset>
	</form>
</div>
