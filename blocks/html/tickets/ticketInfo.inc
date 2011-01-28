<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 */
$location = View::escape($this->ticket->getLocation());
$person = $this->ticket->getPerson() ? $this->ticket->getPerson()->getFullname() : '';
echo "
<h1>Ticket #{$this->ticket->getId()}</h1>
<div>$location</div>
<table>
	<tr><th>Date Opened</th>
		<td>{$this->ticket->getDate('Y-m-d')}</td>
	</tr>
	<tr><th>Received By</th>
		<td>$person</td>
	</tr>
</table>
";