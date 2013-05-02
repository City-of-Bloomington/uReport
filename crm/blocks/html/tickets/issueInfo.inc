<div class="issueInfo">
<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Issue $this->issue
 * @param bool  $this->disableButtons
 */
	include APPLICATION_HOME.'/blocks/html/tickets/partials/issueHeader.inc';

	// Don't display any personal information from the issue unless the user is authenticated
	if (userIsAllowed('people','view')) {
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
		if ($enteredBy)      { echo "<li><label>Created By:   </label> $enteredBy     </li>"; }
		if ($reportedBy)     { echo "<li><label>Reported By:  </label> $reportedBy    </li>"; }
		if ($contactMethod)  { echo "<li><label>Contacted Via:</label> $contactMethod </li>"; }
		if ($responseMethod) { echo "<li><label>Respond Via:  </label> $responseMethod</li>"; }
		if ($labels)         { echo "<li><label>Labels:       </label> $labels        </li>"; }
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

	include APPLICATION_HOME.'/blocks/html/tickets/issueAttachments.inc';

	// Don't display any personal information from the issue unless the user is authenticated
	if (userIsAllowed('people','view')) {
		$this->responses = $this->issue->getResponses();
		include APPLICATION_HOME.'/blocks/html/tickets/responseList.inc';
	}

	$this->history = $this->issue->getHistory();
	if (count($this->history)) {
		include APPLICATION_HOME.'/blocks/html/tickets/history.inc';
	}
?>
</div>