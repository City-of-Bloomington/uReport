<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Person $this->person
 */
$assigned = count($this->person->getTickets('assigned',  ['status'=>'open']));
$reported = count($this->person->getTickets('reportedBy',['status'=>'open']));
echo "
<div class=\"stats\">
	<table>
		<tr><th>{$this->_('tickets_open_assigned')}</th><td>$assigned</td></tr>
		<tr><th>{$this->_('tickets_open_reported')}</th><td>$reported</td></tr>
	</table>
</div>
";
