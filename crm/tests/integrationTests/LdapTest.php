<?php
/**
 * @copyright 2013-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Blossom\Classes\Employee;

$_SERVER['SITE_HOME'] = __DIR__;
require_once '../../bootstrap.inc';

class LdapTest extends PHPUnit_Framework_TestCase
{
	public function testSearch()
	{
		global $DIRECTORY_CONFIG;

		$username = preg_replace('/@.*/','',$DIRECTORY_CONFIG['Employee']['DIRECTORY_ADMIN_BINDING']);

		$employee = new Employee($username);
		$this->assertEquals($employee->getUsername(), $username);
	}
}
