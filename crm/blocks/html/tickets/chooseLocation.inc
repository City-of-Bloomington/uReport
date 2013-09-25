<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 */
$this->template->addToAsset('scripts',YUI.'/yui/yui-min.js');
$this->template->addToAsset('scripts',BASE_URL.'/js/tickets/chooseLocation.js');

$return_url = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
$url = BASE_URL."/locations?return_url=$return_url";
echo "
<div id=\"locationChooser\">
	<div class=\"hd\">
		<a id=\"findAddressButton\" class=\"btn\" href=\"$url\">
			<span class=\"icon-globe\"></span>
			{$this->translate('labels.find_location')}
		</a>
	</div>
	<div class=\"bd\">
";
	if ($this->ticket->getLocation()) {
		$block = new Block(
			'locations/locationInfo.inc',
			array('location'=>$this->ticket->getLocation(), 'disableButtons'=>true)
		);
		echo $block->render('html', $this->template);

		$block = new Block(
			'tickets/ticketList.inc',
			array(
				'ticketList'=>new TicketList(array('location'=>$this->ticket->getLocation())),
				'title'=>$this->translate('labels.tickets_for_location'),
				'disableLinks'=>true
			)
		);
		echo $block->render('html', $this->template);
	}
echo "
	</div>
</div>
";
