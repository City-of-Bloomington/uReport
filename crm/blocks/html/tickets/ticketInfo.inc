<?php
/**
 * @copyright 2011-2021 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Ticket $this->ticket
 * @param bool   $this->disableButtons
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;

use Blossom\Classes\Block;

$ticket_id = $this->ticket->getId();

$status = $this->ticket->getStatus();
if ($this->ticket->getSubstatus()) {
	$status.= ': '.$this->ticket->getSubstatus();
}

$category = $this->ticket->getCategory_id()
	? self::escape($this->ticket->getCategory()->getName())
	: '';
$issueType = $this->ticket->getIssueType_id()
    ? self::escape($this->ticket->getIssueType()->getName())
    : '';
$contactMethod = $this->ticket->getContactMethod_id()
    ? self::escape($this->ticket->getContactMethod()->getName()).':'
    : '';
$description = self::escape($this->ticket->getDescription());
$description = $contactMethod || $description
    ? "<p>$contactMethod $description</p>"
    : '';
$description = $description ? "<p>$description</p>" : '';

$fields  = '';
$canView = Person::isAllowed('people', 'view');
$person  = $this->ticket->getReportedByPerson();
if ($person && ($person->getUsername() || $canView)) {
    $uri    = BASE_URI.'/people/view?person_id='.$person->getId();
    $name   = "<a href=\"$uri\">{$person->getFullname()}</a>";
    $fields.= "<dl><dt>{$this->_('reportedByPerson')}</dt><dd>$name</dd></dl>";
}
$person = $this->ticket->getAssignedPerson();
if ($person && ($person->getUsername() || $canView)) {
    $uri    = BASE_URI.'/people/view?person_id='.$person->getId();
    $name   = "<a href=\"$uri\">{$person->getFullname()}</a>";
    $fields.= "<dl><dt>{$this->_('assignedPerson_id')}</dt><dd>$name</dd></dl>";
}

$cf = $this->ticket->getCategory_id() ? $this->ticket->getCategory()->getCustomFields() : null;
if ($cf) {
    $data = $this->ticket->getCustomFields();
    foreach ($cf as $definition) {
        $fieldname = $definition->name;
        $label = isset($definition->label) ? self::escape($definition->label) : self::escape($fieldname);
        $value = isset($data->$fieldname)  ? $data->$fieldname : '';
        if (is_string($value)) { $value = self::escape($value); }
        if (is_object($value)) {
            $temp = [];
            foreach ($value as $k=>$v) { $temp[] = $v; }
            $value = $temp;
        }
        if (is_array($value)) { $value = implode(', ', $value); }

        if ($value) { $fields.= "<dl><dt>$label</dt><dd>$value</dd></dl>"; }
    }
}
?>
<section class="ticketInfo">
    <header>
        <h1><span class="status <?= $status; ?>"><?= $status; ?></span>
            <a href="<?= $this->ticket->getURL(); ?>" class="ticket_id">#<?= $ticket_id; ?></a>
        </h1>
        <div class="tools">
        <?php
            if (!$this->disableButtons && $this->ticket) {
                $this->_include('tickets/partials/actionLinks.inc');
            }
        ?>
        </div>
        <h2 class="category"><?= $category; ?>: <?= $issueType; ?></h2>
    </header>
    <?php
        echo $description;
        echo $fields;

        $block = new Block('tickets/issueAttachments.inc', ['ticket'=>$this->ticket, 'disableButtons'=>true]);
        echo $block->render('html', $this->template);
    ?>
</section>
<script type="text/javascript">
	CRM.ticket_id = <?= $ticket_id; ?>;
</script>
