<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Issue $this->issue
 */
use Application\Models\TicketTable;
use Blossom\Classes\Block;
use Blossom\Classes\Url;

$this->template->addToAsset('scripts', JQUERY.'/jquery.min.js');
$this->template->addToAsset('scripts', BASE_URI.'/js/tickets/choosePerson.js');

$return_url = new Url($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
$url = BASE_URL."/people?return_url=$return_url";
echo "
<div id=\"reportedByPersonChooser\">
	<div class=\"hd\">
		<h3>{$this->_('reportedByPerson')}</h3>
		<a id=\"findPersonButton\" class=\"user btn\" href=\"$url\">
			{$this->_('find_person')}
		</a>
	</div>
	<div class=\"bd\">
";
	$person = $this->issue->getReportedByPerson();
	if ($person) {
		$block = new Block('people/personInfo.inc', ['person'=>$person, 'disableButtons'=>true]);
		echo $block->render('html', $this->template);


		$table = new TicketTable();
		$reportedTickets = $table->find(['reportedByPerson_id'=>$person->getId()], null, 10);
		if (count($reportedTickets)) {
			$block = new Block('tickets/ticketList.inc', [
                'ticketList'    => $reportedTickets,
                'title'         => $this->_('tickets_reported'),
                'disableButtons'=> true,
                'disableLinks'  => true
            ]);
			if (count($reportedTickets) >= 10) {
				$block->moreLink = BASE_URL."/tickets?reportedByPerson_id={$person->getId()}";
			}
			echo $block->render('html', $this->template);
		}
	}
echo "
	</div>
</div>
";
