<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param string $this->location
 * @param bool $this->disableButtons
 */
if ($this->location) {
	$url = new URL(BASE_URL.'/locations/view?location='.$this->location);
	$location = View::escape($this->location);

	echo "
	<div class=\"locationInfo\">
		<h2><a href=\"$url\" class=\"location\">$location</a></h2>
	</div>
	";
}
