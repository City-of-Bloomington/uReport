<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Person $this->person
 */
$assigned = count($this->person->getTickets('assigned',  ['status'=>'open']));
$reported = count($this->person->getTickets('reportedBy',['status'=>'open']));
echo "
<div class=\"stats\">
	<table>
		<tr><th>{$this->translate('labels.tickets_open_assigned')}</th><td>$assigned</td></tr>
		<tr><th>{$this->translate('labels.tickets_open_reported')}</th><td>$reported</td></tr>
	</table>
</div>
";
