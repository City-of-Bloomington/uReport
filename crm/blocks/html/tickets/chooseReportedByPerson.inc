<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Issue $this->issue
 */
$this->template->addToAsset('scripts',YUI.'/yui/yui-min.js');
$this->template->addToAsset('scripts',BASE_URL.'/js/tickets/choosePerson.js');

$return_url = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
$url = BASE_URL."/people?return_url=$return_url";
echo "
<div id=\"reportedByPersonChooser\">
	<div class=\"hd\">
		<h3>Reporting Person</h3>
		<a id=\"findPersonButton\" class=\"edit button\" href=\"$url\">
			Lookup Person
		</a>
	</div>
	<div class=\"bd\">
";
	$person = $this->issue->getReportedByPerson();
	if ($person) {
		$block = new Block(
			'people/personInfo.inc',
			array('person'=>$person, 'disableButtons'=>true)
		);
		echo $block->render('html', $this->template);


		$reportedTickets = new TicketList();
		$reportedTickets->find(array('reportedByPerson_id'=>$person->getId()), null, 10);
		if (count($reportedTickets)) {
			$block = new Block(
				'tickets/ticketList.inc',
				array(
					'ticketList'    => $reportedTickets,
					'title'         => 'Reported Cases',
					'disableButtons'=> true,
					'disableLinks'  => true
				)
			);
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