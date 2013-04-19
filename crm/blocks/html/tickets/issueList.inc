<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param IssueList $this->issueList
 * @param Ticket    $this->ticket
 * @param bool      $this->disableButtons
 * @param bool      $this->disableComments
 */
$addButton = '';
if (!$this->disableButtons && $this->ticket && userIsAllowed('issues','add')) {
	$addButton = "
	<a class=\"btn\"
		href=\"".BASE_URL."/issues/update?ticket_id={$this->ticket->getId()}\">
		<span class=\"icon-plus\"></span>
		Add Issue
	</a>
	";
}
echo "
<div class=\"issueList\">
	<h1>Associated Issues $addButton</h1>
";
	foreach ($this->issueList as $issue) {
		$block = new Block(
			'tickets/issueInfo.inc',
			array(
				'issue'          => $issue,
				'disableButtons' => $this->disableButtons,
				'disableComments'=> $this->disableComments
			)
		);
		echo $block->render('html',$this->template);
	}
echo "
</div>
";