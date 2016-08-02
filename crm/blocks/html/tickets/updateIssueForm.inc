<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Ticket $this->ticket
 */
?>
<section class="updateIssueForm">
    <header>
        <h1><?= $this->_('update_ticket'); ?></h1>
    </header>
	<form method="post" action="<?= BASE_URI; ?>/tickets/update">
		<fieldset><legend><?= $this->_('info_issue'); ?></legend>
			<input name="ticket_id" type="hidden" value="<?= $this->ticket->getId(); ?>" />
			<?php
				$this->_include('tickets/issueFields.inc');

				$this->category = $this->ticket->getCategory();
				$this->_include('tickets/customFieldsForm.inc');

				$this->_include('tickets/partials/submitAndCancelButtons.inc');
			?>
		</fieldset>
	</form>
</section>
