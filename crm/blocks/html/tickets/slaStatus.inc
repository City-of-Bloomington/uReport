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
	$percentage = round($daysPassed/$days*100);
	if ($this->ticket->getStatus() == 'open') {
		$percentageText = $percentage > 100 ? "overdue" : "should have been completed by now";
		echo "
		<div class=\"slaStatus $class\">
			<p>
			This ticket is supposed to be handled in $days days.
			$daysPassed days have already passed.
			$percentage% $percentageText.
			</p>
		</div>
		";
	}
	else {
		$percentageText = $percentage > 100 ? "overdue" : "of the expected time";
		echo "
		<div class=\"slaStatus $class\">
			<p>
			This ticket was supposed to be handled in $days days.
			$daysPassed days passed before it was closed.
			Completion was $percentage% $percentageText.
			</p>
		</div>
		";
	}
}