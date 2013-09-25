<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param string $this->location
 * @param bool $this->disableButtons
 */
if ($this->location) {
	$url = new URL(BASE_URL.'/locations/view?location='.$this->location);
	$location = View::escape($this->location);

	$addTicketButton = '';
	if (!$this->disableButtons && userIsAllowed('tickets', 'add')) {
		$addTicketButton = "
		<a class=\"btn\" href=\"".BASE_URI."/tickets/add?location={$this->location}\">
			<span class=\"icon-plus\"></span>
			{$this->translate('labels.add_new_ticket')}
		</a>
		";
	}

	echo "
	<div class=\"locationInfo\">
		<h2><a href=\"$url\" class=\"location\">$location</a></h2>
		$addTicketButton
	</div>
	";
}
