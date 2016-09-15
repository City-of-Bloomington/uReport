<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param TicketHistory $this->ticketHistory
 */
?>
<section class="actionForm">
    <header>
        <h1><?= $this->template->title; ?></h1>
    </header>
	<form method="post" action="<?= BASE_URL; ?>/tickets/recordAction">
		<fieldset>
			<input name="ticket_id" type="hidden" value="<?= $this->ticketHistory->getTicket_id(); ?>" />
			<input name="action_id" type="hidden" value="<?= $this->ticketHistory->getAction_id(); ?>" />
			<?php
                $helper = $this->template->getHelper('field');
                echo $helper->field([
                    'name'  => 'actionDate',
                    'id'    => 'actionDate',
                    'label' => $this->_('date'),
                    'type'  => 'date',
                    'value' => $this->ticketHistory->getActionDate(DATE_FORMAT)
                ]);
                echo $helper->field([
                    'name'  => 'notes',
                    'id'    => 'notes',
                    'label' => $this->_(['comment', 'comments', 2]),
                    'type'  => 'textarea',
                    'value' => self::escape($this->ticketHistory->getNotes())
                ]);

                $helper = $this->template->getHelper('saveAndCancelButtons');
                echo $helper->saveAndCancelButtons(
                    BASE_URI.'/tickets/view?ticket_id='.$this->ticketHistory->getTicket_id()
                );
			?>

		</fieldset>
	</form>
</section>
