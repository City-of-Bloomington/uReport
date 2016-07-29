<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Ticket $this->ticket
 */
use Application\Models\ContactMethodTable;
?>
<section class="responseForm">
    <header>
        <h1><?= $this->_('add_response'); ?></h1>
    </header>
	<form method="post" action="<?= BASE_URI; ?>/tickets/respond">
		<fieldset><legend><?= $this->_('how_did_you_respond', 'messages'); ?></legend>
			<input name="ticket_id" type="hidden" value="<?= $this->ticket->getId(); ?>" />
			<?php
                $helper = $this->template->getHelper('field');

                $options = [];
                $people = $this->ticket->getPeople();
                foreach ($people as $p) { $options[] = ['value'=>$p->getId(), 'label'=>self::escape($p->getFullname())]; }
                echo $helper->field([
                    'name'    => 'person_id',
                    'id'      => 'person_id',
                    'label'   => $this->_('person'),
                    'type'    => 'select',
                    'options' => $options
                ]);

                $options = [];
                $table   = new contactMethodTable();
                $list    = $table->find();
                foreach ($list as $m) { $options[] = ['value'=>$m->getId(), 'label'=>self::escape($m->getName())]; }
                echo $helper->field([
                    'name'     => 'contactMethod_id',
                    'id'       => 'contactMethod_id',
                    'label'    => $this->_('contactMethod'),
                    'value'    => $this->ticket->getContactMethod_id(),
                    'required' => true,
                    'type'     => 'select',
                    'options'  => $options
                ]);

                echo $helper->field([
                    'name'  => 'notes',
                    'id'    => 'notes',
                    'label' => $this->_(['comment', 'comments', 2]),
                    'type'  => 'textarea'
                ]);

				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons($this->ticket->getURL());
			?>
		</fieldset>
	</form>
</section>
