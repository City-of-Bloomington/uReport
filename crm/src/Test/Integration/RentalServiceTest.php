<?php
/**
 * @copyright 2013-2019 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Test\Integration;

use PHPUnit\Framework\TestCase;
use Application\Models\RentalService;

class AddressServiceTest extends TestCase
{
	public function testGetOwners()
	{
		$result = RentalService::getOwnerNames('801 W 4th');
		$this->assertEquals(1, count($result));
	}
}
