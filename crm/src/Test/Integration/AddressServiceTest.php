<?php
/**
 * @copyright 2013-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Test\Integration;

use PHPUnit\Framework\TestCase;
use Application\Models\AddressService;

class AddressServiceTest extends TestCase
{
    public function testFieldDefinitions()
    {
        $result = call_user_func(ADDRESS_SERVICE.'::customFieldDefinitions');
        $this->assertEquals(2, count($result));
    }

	public function testGetLocationData()
	{
        $result = call_user_func(ADDRESS_SERVICE.'::getLocationData', '410 W 4th');
		$this->assertEquals('410 W 4th ST',  $result['location'],                'Failed loading location results from Master Address');
		$this->assertEquals('Bloomington',   $result['township'],                'Failed reading township from Master Address');
		$this->assertEquals('Prospect Hill', $result['neighborhoodAssociation'], 'Failed reading Neighorhood Association from Master Address');
	}

	public function testSearchAddresses()
	{
        $result = call_user_func(ADDRESS_SERVICE.'::searchAddresses', 'Somersbe Pl');
		$this->assertEquals(20, count($result));
	}
}
