<?php
/**
 * @copyright 2011-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param string $this->location
 * @param bool $this->disableButtons
 */
use Application\Models\Person;
use Blossom\Classes\View;
use Blossom\Classes\Url;

if ($this->location) {
	$url = new Url(BASE_URL.'/locations/view?location='.$this->location);
	$location = View::escape($this->location);

	$addTicketButton = '';
	if (!$this->disableButtons && Person::isAllowed('tickets', 'add')) {
		$h = $this->template->getHelper('buttonLink');
		$addTicketButton = $h->buttonLink(
			BASE_URI.'/tickets/add?location='.$this->location,
			$this->translate('labels.add_new_ticket'),
			'add'
		);
	}

	echo "
	<div class=\"locationInfo\">
		<h2><a href=\"$url\" class=\"location\">$location</a></h2>
		$addTicketButton
	</div>
	";
}
