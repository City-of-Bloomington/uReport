<?php
/**
 * @copyright 2013-2019 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Test\Integration;

use PHPUnit\Framework\TestCase;
use Application\Models\Ticket;

class TicketTest extends TestCase
{
	private $data = [
		'location'=>'410 W 4th ST',
		'city'    =>'Bloomington',
		'state'   =>'IN',
		'zip'     =>'470404'
	];

	public function testUpdateSetsCoordinatesForLocation()
	{
		$ticket = new Ticket();
		$ticket->handleUpdate(['location'=>$this->data['location']]);

		$this->assertNotEmpty($ticket->getLatitude());
		$this->assertNotEmpty($ticket->getLongitude());
	}

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
		$v = $ticket->getLatitude();
		echo '*';
		print_r($v);
		echo "*\n";

		$this->assertNull($ticket->getLatitude ());
		$this->assertNull($ticket->getLongitude());
	}

	public function testSetAddressServiceData()
	{
		$ticket = new Ticket();
		$ticket->setAddressServiceData($this->data);
		$this->assertEquals($this->data['location'], $ticket->getLocation());
		$this->assertEquals($this->data['city'],     $ticket->getCity());
		$this->assertEquals($this->data['state'],    $ticket->getState());
		$this->assertEquals($this->data['zip'],      $ticket->getZip());
	}

	public function testSetAddressServiceDataReplacesLocation()
	{
		$ticket = new Ticket();
		// Here's what we get from the user via Google Maps
		$ticket->setLocation('351 South Washington Street');
		// We look that up in the AddressService and get this string
		$ticket->setAddressServiceData(array(
			'location'=>'351 S Washington'
		));

		$this->assertEquals('351 S Washington', $ticket->getLocation(), 'Address string was not updated from AddressService');
	}

	public function testAddressServiceDataDoesNotLatLong()
	{
		$ticket = new Ticket();
		$ticket->setLocation('Somewhere');
		$ticket->setLatitude(37);
		$ticket->setLongitude(-80);

		$ticket->setAddressServiceData($this->data);
		$this->assertEquals($this->data['location'], $ticket->getLocation(), 'Address string was not updated from AddressService');
		$this->assertEquals(37, $ticket->getLatitude(), 'Latitude was changed from AddressService');
		$this->assertEquals(-80, $ticket->getLongitude(), 'Longitude was changed from AddressService');
	}
}
