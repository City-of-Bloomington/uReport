<?php
/**
 * @copyright 2011-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 * @param bool $this->disableButtons
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;

use Blossom\Classes\View;
use Blossom\Classes\Url;

$location = View::escape($this->ticket->getLocation());
$location = $location
	? "<a href=\"".BASE_URL."/locations/view?location=".urlencode($location)."\">$location</a>"
	: '';

if (Person::isAllowed('people','view')) {
	$url = new Url(BASE_URL.'/people/view.php');
	$personFields = array('enteredBy','assigned','referred');
	foreach ($personFields as $field) {
		$$field = '';
		$get = 'get'.ucfirst($field).'Person';
		$person = $this->ticket->$get();
		if ($person) {
			$url->person_id = "{$person->getId()}";
			$$field = "<a href=\"$url\">".View::escape($person->getFullname())."</a>";
		}

	}
}

$status = $this->ticket->getStatus();
if ($this->ticket->getSubstatus()) {
	$status.= ': '.$this->ticket->getSubstatus();
}

$editStatusButton     = '';
$deleteButton         = '';
$changeCategoryButton = '';
$changeLocationButton = '';
$assignButton         = '';
$referralButton       = '';

if (!$this->disableButtons && Person::isAllowed('tickets','update')) {
	$helper = $this->template->getHelper('buttonLink');
	$editStatusButton = $helper->buttonLink(
		BASE_URI.'/tickets/changeStatus?ticket_id='.$this->ticket->getId(),
		$this->translate('labels.change_status'),
		'edit',
		ButtonLink::SIZE_ICON
	);
	if (Person::isAllowed('tickets', 'delete')) {
		$deleteButton = $helper->buttonLink(
			BASE_URI.'/tickets/delete?ticket_id='.$this->ticket->getId(),
			$this->translate('labels.delete_ticket'),
			'delete'
		);
	}

	if ($this->ticket->getStatus()!='closed') {
		$changeCategoryButton = $helper->buttonLink(
			BASE_URI.'/tickets/changeCategory?ticket_id='.$this->ticket->getId(),
			$this->translate('labels.change_category'),
			'edit',
			ButtonLink::SIZE_ICON
		);
		$changeLocationButton = $helper->buttonLink(
			BASE_URI.'/tickets/changeLocation?ticket_id='.$this->ticket->getId(),
			$this->translate('labels.change_location'),
			'edit',
			ButtonLink::SIZE_ICON
		);
		if (Person::isAllowed('people','view')) {
			$assignButton = $helper->buttonLink(
				BASE_URI.'/tickets/assign?ticket_id='.$this->ticket->getId(),
				$this->translate('labels.assign_ticket'),
				'edit',
				ButtonLink::SIZE_ICON
			);
			$referralButton = $helper->buttonLink(
				BASE_URI.'/tickets/refer?ticket_id='.$this->ticket->getId(),
				$this->translate('labels.refer_ticket'),
				'edit',
				ButtonLink::SIZE_ICON
			);
		}
	}

}

$category = $this->ticket->getCategory_id()
	? View::escape($this->ticket->getCategory()->getName())
	: '';
echo "
<div class=\"ticketInfo twoColumnlist\">
	<h2><a href=\"{$this->ticket->getURL()}\">Case #{$this->ticket->getId()}</a> $deleteButton</h2>
	<ul>
		<li><label>Category:</label>
			$category
			$changeCategoryButton
		</li>
		<li><label>Location:</label>
			$location
			$changeLocationButton
		</li>
		<li><label>Status:</label>
			$status
			$editStatusButton
		</li>
";
if (Person::isAllowed('people','view')) {
	echo "
		<li><label>Assigned To:</label>
			$assigned
			$assignButton
		</li>
		<li><label>Referred To:</label>
			$referred
			$referralButton
		</li>
		<li><label>Created By:</label>
			$enteredBy
		</li>
	";
}
	$client = $this->ticket->getClient();
	if ($client) {
		$name = View::escape($client->getName());
		echo "
		<li>
			<label>Received Via:</label>
			$name
		</li>
		";
	}
echo "
		<li><label>Date Opened:</label>
			{$this->ticket->getEnteredDate(DATE_FORMAT)}
		</li>
		<li><label>Date Closed:</label>
			{$this->ticket->getClosedDate(DATE_FORMAT)}
		</li>
		<li><label>Last Modified:</label>
			{$this->ticket->getLastModified(DATE_FORMAT)}
		</li>
	</ul>
</div>
<script type=\"text/javascript\">
	CRM.ticket_id = {$this->ticket->getId()};
</script>
";
$this->template->addToAsset('scripts', JQUERY.'/jquery.min.js');
$this->template->addToAsset('scripts', BASE_URI.'/js/tickets/actionForms.js');
