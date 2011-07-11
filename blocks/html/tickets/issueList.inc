<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
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
	<h1>Associated Issues 	$addButton</h1>
";
	foreach ($this->issueList as $index=>$issue) {
		$block = new Block(
			'tickets/issueInfo.inc',
			array(
				'ticket'=>$this->ticket,'issue'=>$issue,'index'=>$index,
				'disableButtons'=>$this->disableButtons,'disableComments'=>$this->disableComments
			)
		);
		echo $block->render('html',$this->template);
	}
echo "
</div>
";