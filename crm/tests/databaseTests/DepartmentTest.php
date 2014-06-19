<?php
/**
 * @copyright 2013-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\Department;

require_once './DatabaseTestCase.php';

class DepartmentTest extends DatabaseTestCase
{
	private $inUseDepartment  = 1;
	private $unusedDepartment = 2;

	public function getDataSet()
	{
		return $this->createMySQLXMLDataSet(__DIR__.'/testData/departmentTestData.xml');
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
