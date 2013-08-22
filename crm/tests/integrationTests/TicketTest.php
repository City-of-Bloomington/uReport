<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
require_once __DIR__.'/../../configuration.inc';

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
