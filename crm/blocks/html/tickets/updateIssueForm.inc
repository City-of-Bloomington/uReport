<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Ticket $this->ticket
 * @param Issue  $this->issue
 */
$title = is_null($this->issue->getId()) ? 'Add Issue' : 'Edit Issue';
?>
<div class="updateIssueForm">
	<h2><?= $title; ?></h2>
	<form method="post" action="<?= $_SERVER['REDIRECT_URL']; ?>">
		<fieldset><legend><?= $this->_('info_issue'); ?></legend>
			<input name="ticket_id" type="hidden" value="<?= $this->ticket->getId(); ?>" />
			<input name="issue_id"  type="hidden" value="<?= $this->issue->getId(); ?>" />
			<?php
				$this->_include('tickets/issueFields.inc');

				$this->category = $this->ticket->getCategory();
				$this->_include('tickets/issueCustomFields.inc');

				$this->_include('tickets/partials/submitAndCancelButtons.inc');
			?>
		</fieldset>
	</form>
</div>
