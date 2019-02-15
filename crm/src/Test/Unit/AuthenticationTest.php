<?php
/**
 * @copyright 2013-2019 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Application\Models\Person;

class AuthenticationTest extends TestCase
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
