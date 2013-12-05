<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
require_once './configuration.inc';
class AuthenticationTest extends PHPUnit_Framework_TestCase
{
	private $testUsername = 'username';
	private $testPassword = 'test';

	public function testAuthenticate()
	{
		$user = new Person();
		$user->setUsername($this->testUsername);
		$user->setPassword($this->testPassword);

		$this->assertTrue($user->authenticate($this->testPassword));
	}
}
