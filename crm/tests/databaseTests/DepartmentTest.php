<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
require_once "PHPUnit/Extensions/Database/TestCase.php";
require_once __DIR__.'/DatabaseTestCase.php';

class DepartmentTest extends DatabaseTestCase
{
	private $inUseDepartment  = 1;
	private $unusedDepartment = 2;

	public function getDataSet()
	{
		return $this->createMySQLXMLDataSet(__DIR__.'/departmentTestData.xml');
	}

	public function testIsSafeToDelete()
	{
		$department = new Department($this->inUseDepartment);
		$this->assertFalse($department->isSafeToDelete());

		$department = new Department($this->unusedDepartment);
		$this->assertTrue($department->isSafeToDelete());
	}

	/**
	 * @expectedException Exception
	 */
	public function testSafeDeleteHandling()
	{
		$department = new Department($this->unusedDepartment);
		$department->delete();

		// The department should be gone from the database, now
		$department = new Department($this->unusedDepartment);
	}
}
