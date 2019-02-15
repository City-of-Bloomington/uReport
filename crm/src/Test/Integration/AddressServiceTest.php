<?php
/**
 * @copyright 2013-2019 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Test\Integration;

use PHPUnit\Framework\TestCase;
use Application\Models\AddressService;

class AddressServiceTest extends TestCase
{
	public function testParseAddress()
	{
		$result = AddressService::parseAddress('410 W 4th');
		$this->assertEquals('410',  $result->street_number);
		$this->assertEquals('W',    $result->direction);
		$this->assertEquals('4th',  $result->street_name);
	}

	public function testGetLocationData()
	{
		$result = AddressService::getLocationData('410 W 4th');
		$this->assertEquals('410 W 4th ST', $result['location']);
	}

	public function testSearchAddresses()
	{
		$result = AddressService::searchAddresses('Somersbe Pl');
		$this->assertEquals(40, count($result));
	}
}
