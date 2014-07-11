<?php
/**
 * @copyright 2013-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\AddressService;

$_SERVER['SITE_HOME'] = __DIR__;
require_once '../../configuration.inc';

class AddressServiceTest extends PHPUnit_Framework_TestCase
{
	public function testParseAddress()
	{
		$result = AddressService::parseAddress('410 W 4th');
		$this->assertEquals('410',  $result->street_number);
		$this->assertEquals('WEST', $result->direction);
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
