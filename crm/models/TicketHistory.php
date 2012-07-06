<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class TicketHistory extends History
{
	protected $tablename = 'ticketHistory';
	public function __construct($id=null) { parent::__construct($id); }
}