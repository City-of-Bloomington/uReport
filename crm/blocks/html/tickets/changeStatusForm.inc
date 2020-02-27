<?php
/**
 * Form for finalizing a status change.
 *
 * The ticket will already have the new status set.
*
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Ticket $this->ticket
 */
use Application\Models\Action;
use Application\Models\Person;
use Application\Models\SubstatusTable;

$fields = ['id', 'status', 'substatus_id'];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f = $this->ticket->$get();
}
$title  = $status === 'closed' ? $this->_('ticket_close') : $this->_('ticket_open');
$action = $this->ticket->getStatus() === 'open' ? 'open' : 'close';
?>
<section class="changeStatusForm">
    <header>
        <h1><?= $title ?></h1>
    </header>
	<form method="post" action="<?= BASE_URI; ?>/tickets/<?= $action; ?>">
		<fieldset>
			<input name="ticket_id" type="hidden" value="<?= $id; ?>" />
			<?php
				// Preserve any extra parameters passed in
				foreach ($_REQUEST as $key=>$value) {
					if ($key!='ticket_id' && $key!='return_url') {
						$key   = self::escape($key);
						$value = self::escape($value);
						echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />\n";
					}
				}

				echo $this->_('tickets/missingClosingComment', 'errors');

				$helper = $this->template->getHelper('field');

                $options = [];
                // Open tickets do not require substatus
                // Closed tickets must have a substatus
                if ($status === 'open') { $options[] = ['value'=>'', 'label'=>'']; }

                $table = new SubstatusTable();
                $list  = $table->find(['status'=>$status]);
                foreach ($list as $s) {
                    $options[] = ['value'=>$s->getId(), 'label'=>self::escape($s->getName())];
                }
                echo $helper->field([
                    'name'    => 'substatus_id',
                    'id'      => 'substatus_id',
                    'label'   => $this->_('substatus'),
                    'value'   => $substatus_id,
                    'type'    => 'select',
                    'options' => $options
                ]);

                $responseTemplate  = $this->ticket->getCategory()->responseTemplateForAction(new Action($status));
                $people            = $this->ticket->getPeople(true);
                $recipients        = [];
                $userCanViewPeople = Person::isAllowed('people', 'view');
                if ($responseTemplate) {
                    $t = self::escape($responseTemplate->getTemplate());
                    echo "
                    <dl><dt>{$this->_('responseTemplate')}</dt>
                        <dd>$t</dd>
                    </dl>
                    ";

                    foreach ($people as $person) {
                        $name   = self::escape($person->getFullname());
                        $emails = $person->getNotificationEmails();

                        if (count($emails)) {
                            if ($userCanViewPeople) {
                                $name = "<a href=\"{$person->getURL()}\">$name</a>";
                            }
                            $recipients[$person->getId()] = "<li>$name</li>";
                        }
                    }
                    if (count($recipients)) {
                        $list = implode('', $recipients);
                        echo "
                        <dl><dt>{$this->_('recipients')}</dt>
                            <dd><ul>$list</ul></dd>
                        </dl>
                        ";
                    }
                }

                echo $helper->field([
                    'name'     => 'notes',
                    'id'       => 'notes',
                    'label'    => $this->_('comment'),
                    'required' => true,
                    'type'     => 'textarea'
                ]);

				$this->_include('tickets/partials/submitAndCancelButtons.inc');
			?>
		</fieldset>
	</form>
	<?php
        $contacts = [];
        foreach ($people as $person) {
            if (!array_key_exists($person->getId(), $recipients)) {
                $name = self::escape($person->getFullname());
                if ($userCanViewPeople) {
                    $name = "<a href=\"{$person->getURL()}\">$name</a>";
                }
                $contacts[] = "<li>$name</li>";
            }
        }
        if (count($contacts)) {
            $contacts = implode('', $contacts);
            echo "
            <div class=\"responseReminder\">
                <h2>{$this->_('contact_list')}</h2>
                <p>{$this->_('response_reminder', 'messages')}</p>
                <ul>$contacts</ul>
            </div>
            ";
        }
	?>
</section>
