<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
require_once './configuration.inc';
class TicketTest extends PHPUnit_Framework_TestCase
{
	private $data = array(
		'location'=>'Test Location',
		'city'    =>'Bloomington',
		'state'   =>'IN',
		'zip'     =>'470404'
	);

	public function testHandleUpdate()
	{
		$ticket = new Ticket();
		$ticket->handleUpdate(array(
			'location'=> $this->data['location'],
			'city'    => $this->data['city'],
			'state'   => $this->data['state'],
			'zip'     => $this->data['zip']
		));
		$this->assertEquals($this->data['location'], $ticket->getLocation());
		$this->assertEquals($this->data['city'],     $ticket->getCity());
		$this->assertEquals($this->data['state'],    $ticket->getState());
		$this->assertEquals($this->data['zip'],      $ticket->getZip());
	}

	public function testChangingWillUpdateClusters()
	{
		$ticket = new Ticket();
		$this->assertFalse($ticket->willUpdateClustersOnSave());
		$ticket->setLatitude(39.123);
		$this->assertTrue($ticket->willUpdateClustersOnSave());
	}

	public function testLatLngShouldNotAllowZeros()
	{
		$ticket = new Ticket();
		$ticket->setLatitude (0);
		$ticket->setLongitude(0);

		$this->assertNull($ticket->getLatitude ());
		$this->assertNull($ticket->getLongitude());
	}
}
