<?php
/**
 * @copyright 2013-2019 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Test\Integration;

use PHPUnit\Framework\TestCase;
use Blossom\Classes\Employee;

class LdapTest extends TestCase
{
	public function testSearch()
	{
		global $DIRECTORY_CONFIG;

		$username = preg_replace('/@.*/','',$DIRECTORY_CONFIG['Employee']['DIRECTORY_ADMIN_BINDING']);

		$employee = new Employee($username);
		$this->assertEquals($employee->getUsername(), $username);
	}
}
