<?php
/**
 * @copyright 2013-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\Department;
use Application\Models\Action;

require_once './DatabaseTestCase.php';

class DepartmentTest extends DatabaseTestCase
{
	private $inUseDepartment  = 1;
	private $unusedDepartment = 2;

	public function getDataSet()
	{
		return $this->createMySQLXMLDataSet(__DIR__.'/testData/departments.xml');
	}

	public function testSetActions()
	{
        $test       = new Action('test');
        $attempt    = new Action('attempt');
        $department = new Department($this->unusedDepartment);

        $department->setActions([$test->getId(), $attempt->getId()]);
        $actions = $department->getActions();
        $this->assertEquals(2, count($actions));
        $this->assertTrue(in_array($test->getId(),    array_keys($actions)));
        $this->assertTrue(in_array($attempt->getId(), array_keys($actions)));

        $department->setActions([]);
        $actions = $department->getActions();
        $this->assertEquals(0, count($actions));
	}

	public function testSaveActions()
	{
        $test       = new Action('test');
        $attempt    = new Action('attempt');
        $department = new Department($this->unusedDepartment);

        $department->saveActions([$test->getId(), $attempt->getId()]);
        $department = new Department($this->unusedDepartment);
        $actions = $department->getActions();
        $this->assertEquals(2, count($actions));
        $this->assertTrue(in_array($test->getId(),    array_keys($actions)));
        $this->assertTrue(in_array($attempt->getId(), array_keys($actions)));


        $department->saveActions([]);
        $department = new Department($this->unusedDepartment);
        $actions = $department->getActions();
        $this->assertEquals(0, count($actions));
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
