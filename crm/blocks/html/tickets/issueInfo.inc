<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Ticket $this->ticket
 * @param bool   $this->disableButtons
 */
use Application\Models\Person;
?>
<article class="issueInfo">
<?php
	$this->_include('tickets/partials/issueHeader.inc');
	$this->_include('tickets/issueAttachments.inc');

	// Don't display any personal information from the issue unless the user is authenticated
	$userCanViewPeople = Person::isAllowed('people', 'view');

	foreach (['enteredByPerson', 'reportedByPerson'] as $person) {
        $get = 'get'.ucfirst($person);
        $p = $this->ticket->$get();
        if ($p) {
            $$person = self::escape($p->getFullname());
            if ($userCanViewPeople) {
                $href = BASE_URI."/people/view?person_id={$p->getId()}";
                $$person = "<a href=\"$href\">{$$person}</a>";
            }
        }
	}
    $contactMethod  = self::escape($this->ticket->getContactMethod());
    $responseMethod = self::escape($this->ticket->getResponseMethod());
    foreach (['enteredByPerson', 'reportedByPerson', 'contactMethod', 'responseMethod'] as $f) {
        if (!empty($$f)) {
            echo "<dl><dt>{$this->_($f)}</dt><dd>{$$f}</dd></dl>";
        }
    }

    $customFields = $this->ticket->getCategory_id() ? $this->ticket->getCategory()->getCustomFields() : null;
    if ($customFields) {
        $customFieldData = $this->ticket->getCustomFields();
        foreach ($customFields as $definition) {
            $fieldname = $definition->name;
            $label = isset($definition->label) ? self::escape($definition->label) : self::escape($fieldname);
            $value = isset($customFieldData->$fieldname) ? $customFieldData->$fieldname : '';
            $value = is_array($value) ? implode(', ',$value) : self::escape($value);
            if ($value) { echo "<dl><dt>$label</dt><dd>$value</dd>"; }
        }
    }

	if (!$this->disableComments) {
		$description = self::escape($this->ticket->getDescription());
		if ($description) { echo "<div class=\"notes\">$description</div>"; }
	}

	// Don't display any personal information from the issue unless the user is authenticated
	#if ($userCanViewPeople && $this->ticket->getReportedByPerson()) {
	#	$this->responses = $this->ticket->getResponses();
	#	$this->_include('tickets/responseList.inc');
	#}

	#$this->history = $this->ticket->getHistory();
	#if (count($this->history)) {
	#	$this->_include('tickets/history.inc');
	#}
?>
</article>
