<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Issue $this->issue
 * @param bool  $this->disableButtons
 */
use Application\Models\Person;
?>
<div class="issueInfo">
<?php
	$this->_include('tickets/partials/issueHeader.inc');
	$this->_include('tickets/issueAttachments.inc');

	// Don't display any personal information from the issue unless the user is authenticated
	$userCanViewPeople = Person::isAllowed('people', 'view');

    $enteredBy = '';
    $p = $this->issue->getEnteredByPerson();
    if ($p) {
        $enteredBy = self::escape($p->getFullname());
        if ($userCanViewPeople) {
            $href = BASE_URL."/people/view?person_id={$p->getId()}";
            $enteredBy = "<a href=\"$href\">$enteredBy</a>";
        }
    }

    $reportedBy = '';
    $p = $this->issue->getReportedByPerson();
    if ($p) {
        $reportedBy = self::escape($p->getFullname());

        if ($userCanViewPeople) {
            $href = BASE_URL."/people/view?person_id={$p->getId()}";
            $reportedBy = "<a href=\"$href\">$reportedBy</a>";
        }
    }

    $contactMethod  = self::escape($this->issue->getContactMethod());
    $responseMethod = self::escape($this->issue->getResponseMethod());
    echo "<ul class=\"twoColumnlist\">";
    if ($enteredBy)      { echo "<li><label>{$this->_('enteredByPerson' )}:</label> $enteredBy     </li>"; }
    if ($reportedBy)     { echo "<li><label>{$this->_('reportedByPerson')}:</label> $reportedBy    </li>"; }
    if ($contactMethod)  { echo "<li><label>{$this->_('contactMethod'   )}:</label> $contactMethod </li>"; }
    if ($responseMethod) { echo "<li><label>{$this->_('responseMethod'  )}:</label> $responseMethod</li>"; }

    $t = $this->issue->getTicket();
    $customFields = $t->getCategory_id() ? $t->getCategory()->getCustomFields() : null;
    if ($customFields) {
        $customFieldData = $this->issue->getCustomFields();
        foreach ($customFields as $definition) {
            $fieldname = $definition->name;
            $label = isset($definition->label) ? self::escape($definition->label) : self::escape($fieldname);
            $value = isset($customFieldData->$fieldname) ? $customFieldData->$fieldname : '';
            $value = is_array($value) ? implode(', ',$value) : self::escape($value);
            if ($value) { echo "<li><label>$label:</label> $value</li>\n"; }
        }
    }
    echo "
    </ul>
    ";

	if (!$this->disableComments) {
		$description = self::escape($this->issue->getDescription());
		if ($description) { echo "<div class=\"notes\"><h2>Details:</h2>$description</div>"; }
	}

	// Don't display any personal information from the issue unless the user is authenticated
	if ($userCanViewPeople && $this->issue->getReportedByPerson()) {
		$this->responses = $this->issue->getResponses();
		$this->_include('tickets/responseList.inc');
	}

	$this->history = $this->issue->getHistory();
	if (count($this->history)) {
		$this->_include('tickets/history.inc');
	}
?>
</div>
