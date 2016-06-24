<?php
/**
 * @copyright 2011-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param IssueList $this->issueList
 * @param Ticket    $this->ticket
 * @param bool      $this->disableButtons
 * @param bool      $this->disableComments
 */
use Application\Models\Person;
use Blossom\Classes\Block;

$addButton = '';
if (!$this->disableButtons && $this->ticket && Person::isAllowed('issues','add')) {
	$h = $this->template->getHelper('buttonLink');
	$addButton = $h->buttonLink(
		BASE_URL.'/issues/update?ticket_id='.$this->ticket->getId(),
		$this->_('add_issue'),
		'add'
	);
}
echo "
<div class=\"issueList\">
	<h2>{$this->_('ticket_issues')} $addButton</h2>
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
