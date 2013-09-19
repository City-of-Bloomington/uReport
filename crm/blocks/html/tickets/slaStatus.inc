<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 */
$days = $this->ticket->getSlaDays();
if ($days) {
	$status = $this->ticket->getStatus();
	$dateEntered = new DateTime($this->ticket->getEnteredDate());
	$targetDate = $status=='open' ? new DateTime() : new DateTime($this->ticket->getClosedDate());
	$diff = $targetDate->diff($dateEntered);
	$daysPassed = $diff->format('%a');
	$class = $daysPassed >= $days ? 'pastDue' : 'onTrack';
	echo "
	<div class=\"slaStatus $class\">
		<p>
		Most tickets of this type should be closed within $days days,
		although some cases may be longer.
		$daysPassed days have already passed.
		</p>
	</div>
	";
}
