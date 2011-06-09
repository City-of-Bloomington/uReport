<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param IssueList $this->issueList
 * @param Ticket $this->ticket
 * @param bool $this->disableButtons
 * @param bool $this->disableComments
 */
$addButton = '';
if (!$this->disableButtons && $this->ticket && userIsAllowed('Issues')) {
	$addButton = $this->template->linkButton(
		'Add Issue',
		BASE_URL."/tickets/updateIssue.php?ticket_id={$this->ticket->getId()}",
		'add',null, null,'button'
	);
}
echo "
<div class=\"issueList\">
	<h1>Associated Issues</h1>
	$addButton
";
	foreach ($this->issueList as $index=>$issue) {
		$editButton = '';
		$responseButton = '';
		if (!$this->disableButtons && userIsAllowed('Issues')) {
			$editButton = $this->template->linkButton(
				'Edit Issue',
				BASE_URL."/tickets/updateIssue.php?ticket_id={$this->ticket->getId()};index=$index",
				'edit',
				null,
				null,
				'button'
			);

			if ($issue->getReportedByPerson()) {
				$responseButton = $this->template->linkButton(
					'Add Response',
					BASE_URL."/tickets/addResponse.php?ticket_id={$this->ticket->getId()};index=$index",
					'add',
					null,
					null,
					'button'
				);
			}
		}


		$enteredBy = '';
		if ($issue->getEnteredByPerson()) {
			$href = BASE_URL."/people/viewPerson.php?person_id={$issue->getData('enteredByPerson._id')}";
			$enteredBy = View::escape($issue->getData('enteredByPerson.fullname'));
			$enteredBy = "<a href=\"$href\">$enteredBy</a>";
		}

		$reportedBy = '';
		if ($issue->getReportedByPerson()) {
			$href = BASE_URL."/people/viewPerson.php?person_id={$issue->getData('reportedByPerson._id')}";
			$reportedBy = View::escape($issue->getData('reportedByPerson.fullname'));
			$reportedBy = "<a href=\"$href\">$reportedBy</a>";
		}

		$type = View::escape($issue->getType());
		$contactMethod = View::escape($issue->getContactMethod());
		$responseMethod = View::escape($issue->getResponseMethod());
		echo "
		<div class=\"issue\">
		<h2><span class=\"date\">{$issue->getDate(DATE_FORMAT)}</span>
			<span class=\"issueType\">$type</span>
			$editButton
			$responseButton
		</h2>
		<table>
			<thead>
				<tr><th>Created by</th>
					<th>Category</th>
					<th>Reported By</th>
					<th>Contacted Via</th>
					<th>Respond Via</th>
		";
					if (!$this->disableComments) {
						echo '<th>Comments</th>';
					}
		echo "
				</tr>
			</thead>
			<tbody>
				<tr><td>$enteredBy</td>
					<td>{$issue->getData('category.name')}</td>
					<td>$reportedBy</td>
					<td>$contactMethod</td>
					<td>$responseMethod</td>
		";
					if (!$this->disableComments) {
						$notes = View::escape($issue->getNotes());
						echo "<td>$notes</td>";
					}
		echo "
				</tr>
			</tbody>
		</table>
		</div>		
		";

		$this->responses = $issue->getResponses();
		if ($this->responses) {
			include APPLICATION_HOME.'/blocks/html/tickets/responseList.inc';
		}

		$block = new Block('media/mediaList.inc',array(
			'ticket'=>$this->ticket,
			'index'=>$index,
			'mediaList'=>$issue->getMedia(),
			'disableButtons'=>$this->disableButtons)
		);
		echo $block->render('html',$this->template);

		$history = $issue->getHistory();
		if (count($history)) {
			$block = new Block(
				'tickets/history.inc',
				array('history'=>$history,'disableComments'=>$this->disableComments)
			);
			echo $block->render('html',$this->template);
		}
	}
echo "
</div>
";