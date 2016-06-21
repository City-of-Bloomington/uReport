<?php
/**
 * @copyright 2013-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\RentalService;

$_SERVER['SITE_HOME'] = __DIR__;
require_once '../../bootstrap.inc';

class AddressServiceTest extends PHPUnit_Framework_TestCase
{
	public function testGetOwners()
	{
		$result = RentalService::getOwnerNames('801 W 4th');
		$this->assertEquals(1, count($result));
	}
}
