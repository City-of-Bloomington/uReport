<?php
/**
 * @copyright 2013-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\Ticket;

$_SERVER['SITE_HOME'] = __DIR__;
require_once '../../configuration.inc';

class TicketTest extends PHPUnit_Framework_TestCase
{
	private $data = array(
		'location' => '410 W 4th'
	);

	public function testUpdateSetsCoordinatesForLocation()
	{
		$ticket = new Ticket();
		$ticket->handleUpdate(array('location'=>$this->data['location']));

		$this->assertNotEmpty($ticket->getLatitude());
		$this->assertNotEmpty($ticket->getLongitude());
	}
}
