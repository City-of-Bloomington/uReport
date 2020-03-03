<?php
/**
 * @copyright 2011-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Ticket $this->ticket
 * @param Department $this->currentDepartment
 */
use Application\Models\DepartmentTable;

$this->template->addToAsset('scripts', BASE_URI.'/js/tickets/assignForm-'.VERSION.'.js');
?>
<div class="assignTicketForm">
	<form method="post" action="<?= BASE_URI; ?>/tickets/assign">
		<fieldset><legend><?= $this->_('assign_ticket');  ?></legend>
			<input name="ticket_id" type="hidden" value="<?= $this->ticket->getId(); ?>" />
			<?php
				// Preserve any extra parameters passed in
				foreach ($_REQUEST as $key=>$value) {
					if ($key!='ticket_id' && $key!='return_url') {
						$key   = self::escape($key);
						$value = self::escape($value);
						echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />\n";
					}
				}

                $helper  = $this->template->getHelper('field');
                $table   = new DepartmentTable();
                $list    = $table->find();
                $options = [];
                foreach ($list as $d) {
                    $options[] = ['value'=>$d->getId(), 'label'=>self::escape($d->getName())];
                }
                echo $helper->field([
                    'name'    => 'department_id',
                    'id'      => 'department_id',
                    'label'   => $this->_(['department', 'departments', count($list)]),
                    'value'   => $this->currentDepartment ? $this->currentDepartment->getId() : null,
                    'type'    => 'select',
                    'options' => $options
                ]);

				$this->_include('tickets/assignTicketFields.inc');
				$this->_include('tickets/partials/submitAndCancelButtons.inc');
			?>
		</fieldset>
	</form>
</div>
