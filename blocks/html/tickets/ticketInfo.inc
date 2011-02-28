<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 */
$location = View::escape($this->ticket->getLocation());
$enteredBy = $this->ticket->getEnteredByPerson() ? $this->ticket->getEnteredByPerson()->getFullname() : '';
echo "
<div class=\"ticketInfo\">
	<h1>Ticket #{$this->ticket->getId()}</h1>
	<div>$location</div>
	<table>
		<tr><th>Date Opened</th>
			<td>{$this->ticket->getEnteredDate('Y-m-d')}</td>
		</tr>
		<tr><th>Received By</th>
			<td>$enteredBy</td>
		</tr>
	</table>
</div>
";
