<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
require_once __DIR__.'/../../configuration.inc';

class AddressServiceTest extends PHPUnit_Framework_TestCase
{
	public function testGetOwners()
	{
		$result = RentalService::getOwnerNames('801 W 4th');
		$this->assertEquals(1, count($result));
	}
}
