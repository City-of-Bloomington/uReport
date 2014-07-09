<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Issue $this->issue
 * @param bool  $this->disableButtons
 */
use Application\Models\Person;
use Blossom\Classes\View;
?>
<div class="issueInfo">
<?php
	include APPLICATION_HOME.'/blocks/html/tickets/partials/issueHeader.inc';
	include APPLICATION_HOME.'/blocks/html/tickets/issueAttachments.inc';

	// Don't display any personal information from the issue unless the user is authenticated
	if (Person::isAllowed('people','view')) {
		$enteredBy = '';
		$p = $this->issue->getEnteredByPerson();
		if ($p) {
			$enteredBy = View::escape($p->getFullname());

			$href = BASE_URL."/people/view?person_id={$p->getId()}";
			$enteredBy = "<a href=\"$href\">$enteredBy</a>";
		}

		$reportedBy = '';
		$p = $this->issue->getReportedByPerson();
		if ($p) {
			$reportedBy = View::escape($p->getFullname());

			$href = BASE_URL."/people/view?person_id={$p->getId()}";
			$reportedBy = "<a href=\"$href\">$reportedBy</a>";
		}

		$contactMethod  = View::escape($this->issue->getContactMethod());
		$responseMethod = View::escape($this->issue->getResponseMethod());
		$labels = View::escape(implode(', ',$this->issue->getLabels()));
		echo "<ul class=\"twoColumnlist\">";
		if ($enteredBy)      { echo "<li><label>{$this->translate('labels.enteredByPerson' )}:</label> $enteredBy     </li>"; }
		if ($reportedBy)     { echo "<li><label>{$this->translate('labels.reportedByPerson')}:</label> $reportedBy    </li>"; }
		if ($contactMethod)  { echo "<li><label>{$this->translate(array('labels.contactMethod', 'labels.contactMethods', 1))}:</label> $contactMethod </li>"; }
		if ($responseMethod) { echo "<li><label>{$this->translate('labels.responseMethod'  )}:</label> $responseMethod</li>"; }
		if ($labels)         { echo "<li><label>{$this->translate(array('labels.label', 'labels.labels', 2))}:</label> $labels</li>"; }
			$t = $this->issue->getTicket();
			$customFields = $t->getCategory_id() ? $t->getCategory()->getCustomFields() : null;
			if ($customFields) {
				$customFieldData = $this->issue->getCustomFields();
				foreach ($customFields as $definition) {
					$fieldname = $definition->name;
					$label = isset($definition->label) ? View::escape($definition->label) : View::escape($fieldname);
					$value = isset($customFieldData->$fieldname) ? $customFieldData->$fieldname : '';
					$value = is_array($value) ? implode(', ',$value) : View::escape($value);
					if ($value) { echo "<li><label>$label:</label> $value</li>\n"; }
				}
			}
		echo "
		</ul>
		";
	}

	if (!$this->disableComments) {
		$description = View::escape($this->issue->getDescription());
		if ($description) { echo "<div class=\"notes\"><h2>Details:</h2>$description</div>"; }
	}

	// Don't display any personal information from the issue unless the user is authenticated
	if (Person::isAllowed('people','view')
		&& $this->issue->getReportedByPerson()) {
		$this->responses = $this->issue->getResponses();
		include APPLICATION_HOME.'/blocks/html/tickets/responseList.inc';
	}

	$this->history = $this->issue->getHistory();
	if (count($this->history)) {
		include APPLICATION_HOME.'/blocks/html/tickets/history.inc';
	}
?>
</div>
