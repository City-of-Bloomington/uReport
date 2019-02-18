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
	private static $data = [
		'location'=>'410 W 4th ST',
		'city'    =>'Bloomington',
		'state'   =>'IN',
		'zip'     =>'470404'
	];

	public function testSetAddressServiceData()
	{
		$ticket = new Ticket();
		$ticket->setAddressServiceData(self::$data);
		$this->assertEquals(self::$data['location'], $ticket->getLocation());
		$this->assertEquals(self::$data['city'    ], $ticket->getCity());
		$this->assertEquals(self::$data['state'   ], $ticket->getState());
		$this->assertEquals(self::$data['zip'     ], $ticket->getZip());
		$this->assertNull($ticket->getLatitude(),   "Latitude not set from AddressService");
		$this->assertNull($ticket->getLongitude(), "Longitude not set from AddressService");
	}

	public function testSetAddressServiceDataReplacesLocation()
	{
		$ticket = new Ticket();
		// Here's what we get from the user via Google Maps
		$ticket->setLocation('351 South Washington Street');
		// We look that up in the AddressService and get this string
		$ticket->setAddressServiceData(['location'=>'351 S Washington']);

		$this->assertEquals('351 S Washington', $ticket->getLocation(), 'Address string was not updated from AddressService');
	}

	/**
	 * When the location string is not in Master Address,
	 * use the lat/long that was posted.
	 */
	public function testAddressServiceDataDoesNotLatLong()
	{
		$ticket = new Ticket();
		$ticket->setLocation('Somewhere');
		$ticket->setLatitude(37);
		$ticket->setLongitude(-80);

		$ticket->setAddressServiceData(self::$data);
		$this->assertEquals(self::$data['location'], $ticket->getLocation(), 'Address string was not updated from AddressService');
		$this->assertEquals( 37, $ticket->getLatitude(),  'Latitude was changed from AddressService');
		$this->assertEquals(-80, $ticket->getLongitude(), 'Longitude was changed from AddressService');
	}
}
