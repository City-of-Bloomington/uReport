<?php
/**
 * @copyright 2013-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Blossom\Classes\Employee;

$_SERVER['SITE_HOME'] = __DIR__;
require_once '../../configuration.inc';

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
