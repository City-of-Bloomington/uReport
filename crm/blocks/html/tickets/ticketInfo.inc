<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Ticket $this->ticket
 * @param bool $this->disableButtons
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

$fields = '';
$person = $this->ticket->getReportedByPerson();
if ($person) {
    $name = $person->getFullname();
    if (Person::isAllowed('people', 'view')) {
        $uri = BASE_URI.'/people/view?person_id='.$person->getId();
        $name = "<a href=\"$uri\">$name</a>";
    }
    $fields.= "<dl><dt>{$this->_('reportedByPerson')}</dt><dd>$name</dd></dl>";
}
$cf = $this->ticket->getCategory_id() ? $this->ticket->getCategory()->getCustomFields() : null;
if ($cf) {
    $data = $this->ticket->getCustomFields();
    foreach ($cf as $definition) {
        $fieldname = $definition->name;
        $label = isset($definition->label) ? self::escape($definition->label) : self::escape($fieldname);
        $value = isset($data->$fieldname)  ? $data->$fieldname : '';
        $value = is_array($value) ? implode(', ',$value) : self::escape($value);
        if ($value) { $fields.= "<dl><dt>$label</dt><dd>$value</dd></dl>"; }
    }
}

echo "
<section class=\"ticketInfo\">
    <header>
        <h1><span class=\"status\">$status</span>
            <a href=\"{$this->ticket->getURL()}\" class=\"ticket_id\">#{$ticket_id}</a>
        </h1>
        <h2 class=\"category\">$category: $issueType</h2>
    </header>
    $description
    $fields
";
    $block = new Block('tickets/issueAttachments.inc', ['ticket'=>$this->ticket, 'disableButtons'=>true]);
    echo $block->render('html', $this->template);
echo "
</section>
<script type=\"text/javascript\">
	CRM.ticket_id = $ticket_id;
</script>
";
