<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
$notClosed = array('status'=>array('$ne'=>'closed'));

$assigned = count($this->person->getTickets('assigned',$notClosed));
$reported = count($this->person->getTickets('reported',$notClosed));
$referred = count($this->person->getTickets('referred',$notClosed));
echo "
<div class=\"stats\">
	<table>
		<tr><th>Currently assigned tickets</th>
			<td>$assigned</td>
		</tr>
		<tr><th>Outstanding reported tickets</th>
			<td>$reported</td>
		</tr>
		<tr><th>Current referred tickets</th>
			<td>$referred</td>
		</tr>
	</table>
</div>
";
