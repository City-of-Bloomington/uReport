<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Ticket $this->ticket
 */
use Application\Models\SubstatusTable;

$this->template->addToAsset('scripts', JQUERY.'/jquery.min.js');
$this->template->addToAsset('scripts', BASE_URI.'/js/tickets/chooseStatus.js');
?>
<div class="changeStatusForm">
	<form method="post" action="<?= BASE_URI; ?>/tickets/changeStatus">
		<fieldset><legend><?= $this->_('change_status'); ?></legend>
			<input name="ticket_id" type="hidden" value="<?= $this->ticket->getId(); ?>" />
			<?php
				// Preserve any extra parameters passed in
				foreach ($_REQUEST as $key=>$value) {
					if ($key!='ticket_id' && $key!='return_url') {
						$key = self::escape($key);
						$value = self::escape($value);
						echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />\n";
					}
				}

				$helper = $this->template->getHelper('field');

				echo $helper->field([
                    'name'     => 'status',
                    'id'       => 'status',
                    'label'    => $this->_('status'),
                    'value'    => $this->ticket->getStatus(),
                    'required' => true,
                    'type'     => 'select',
                    'options'  => [
                        ['value' => 'open',   'label'=>$this->_('open'  )],
                        ['value' => 'closed', 'label'=>$this->_('closed')]
                    ]
				]);

                $options = [];
                // Open tickets do not require substatus
                // Closed tickets must have a substatus
                if ($this->ticket->getStatus() === 'open') { $options[] = ['value'=>'', 'label'=>'']; }

                $table = new SubstatusTable();
                $list  = $table->find(['status'=>$this->ticket->getStatus()]);
                foreach ($list as $s) {
                    $options[] = ['value'=>$s->getId(), 'label'=>self::escape($s->getName())];
                }
                echo $helper->field([
                    'name'    => 'substatus_id',
                    'id'      => 'substatus_id',
                    'label'   => $this->_('substatus'),
                    'value'   => $this->ticket->getSubstatus_id(),
                    'type'    => 'select',
                    'options' => $options
                ]);

                echo $helper->field([
                    'name'     => 'notes',
                    'id'       => 'notes',
                    'label'    => $this->_('comment'),
                    'required' => true,
                    'type'     => 'textarea'
                ]);

				$this->_include('errorMessages/tickets/missingClosingComment.inc');
				$this->_include('tickets/partials/submitAndCancelButtons.inc');
			?>
		</fieldset>
	</form>
</div>
