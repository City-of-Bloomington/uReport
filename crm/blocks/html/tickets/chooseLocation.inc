<?php
/**
 * @copyright 2011-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 */
use Application\Models\TicketTable;
use Blossom\Classes\Block;
use Blossom\Classes\Url;

$this->template->addToAsset('scripts', JQUERY.'/jquery.min.js');
$this->template->addToAsset('scripts', BASE_URI.'/js/tickets/chooseLocation.js');

$return_url = new Url($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
$url = BASE_URL."/locations?return_url=$return_url";
echo "
<div id=\"locationChooser\">
	<div class=\"hd\">
		<a id=\"findAddressButton\" class=\"btn\" href=\"$url\">
			<span class=\"fa fa-globe\"></span>
			{$this->_('find_location')}
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

		$table = new TicketTable();
		$block = new Block(
			'tickets/ticketList.inc',
			array(
				'ticketList'  => $table->find(array('location'=>$this->ticket->getLocation())),
				'title'       => $this->_('tickets_for_location'),
				'disableLinks'=> true
			)
		);
		echo $block->render('html', $this->template);
	}
echo "
	</div>
</div>
";
