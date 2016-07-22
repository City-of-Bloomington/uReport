<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Ticket $this->ticket
 * @param bool $this->disableButtons
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;

$ticket_id = $this->ticket->getId();

$status = $this->ticket->getStatus();
if ($this->ticket->getSubstatus()) {
	$status.= ': '.$this->ticket->getSubstatus();
}

$category = $this->ticket->getCategory_id()
	? self::escape($this->ticket->getCategory()->getName())
	: '';

$description = '';
$issues = $this->ticket->getIssues();
if (count($issues)) {
    $description = '<p>'.self::escape($issues[0]->getDescription()).'</p>';
}
echo "
<section class=\"ticketInfo\">
    <header>
        <h1><span class=\"status\">$status</span>
            <a href=\"{$this->ticket->getURL()}\" class=\"ticket_id\">#{$ticket_id}</a>
        </h1>
        <h2 class=\"category\">$category</h2>
    </header>
    $description
</section>
<script type=\"text/javascript\">
	CRM.ticket_id = $ticket_id;
</script>
";
$this->template->addToAsset('scripts', JQUERY.'/jquery.min.js');
$this->template->addToAsset('scripts', BASE_URI.'/js/tickets/actionForms.js');
