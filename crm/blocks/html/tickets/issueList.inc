<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param IssueList $this->issueList
 * @param Ticket    $this->ticket
 * @param bool      $this->disableButtons
 * @param bool      $this->disableComments
 */
use Application\Models\Person;
use Blossom\Classes\Block;

$addButton = '';
if (!$this->disableButtons && $this->ticket && Person::isAllowed('issues', 'add')) {
	$h = $this->template->getHelper('buttonLink');
	$addButton = $h->buttonLink(
		BASE_URL.'/issues/update?ticket_id='.$this->ticket->getId(),
		$this->_('add_issue'),
		'add'
	);
}
echo "
<section class=\"issueList\">
    <header>
        <h1>{$this->_('ticket_issues')}</h1>
        <div class=\"tools\">$addButton</div>
    </header>
";
    $block = new Block('tickets/issueInfo.inc');
	foreach ($this->issueList as $issue) {
        $block->issue           = $issue;
        $block->disableButtons  = $this->disableButtons;
        $block->disableComments = $this->disableComments;
		echo $block->render('html',$this->template);
	}
echo "
</section>
";
