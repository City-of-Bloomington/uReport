<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
$assigned = count($this->person->getTickets('assigned',  array('status'=>'open')));
$reported = count($this->person->getTickets('reportedBy',array('status'=>'open')));
$referred = count($this->person->getTickets('referred',  array('status'=>'open')));
echo "
<div class=\"stats\">
	<table>
		<tr><th>{$this->translate('labels.tickets_open_assigned')}</th><td>$assigned</td></tr>
		<tr><th>{$this->translate('labels.tickets_open_reported')}</th><td>$reported</td></tr>
		<tr><th>{$this->translate('labels.tickets_open_referred')}</th><td>$referred</td></tr>
	</table>
</div>
";
