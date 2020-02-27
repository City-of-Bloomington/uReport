<?php
/**
 * @copyright 2013-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\Action;
use Application\Models\Category;
use Application\Models\Department;

require_once './DatabaseTestCase.php';

class DepartmentTest extends DatabaseTestCase
{
	private $inUseDepartment  = 1;
	private $unusedDepartment = 2;

	public function getDataSet()
	{
		return $this->createMySQLXMLDataSet(__DIR__.'/testData/departments.xml');
	}

	public function testGetCategories()
	{
		$department = new Department($this->unusedDepartment);
		$categories = $department->getCategories();
		$this->assertEquals(2, count($categories));
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

	public function testSaveCategories()
	{
		$test    = new Category('Test Category');
		$another = new Category('Another Category');
        $department = new Department($this->unusedDepartment);

        $department->saveCategories([$test->getId(), $another->getId()]);
        $department = new Department($this->unusedDepartment);
        $categories = $department->getCategories();
        $this->assertEquals(2, count($categories));
        $this->assertTrue(in_array($test->getId(),    array_keys($categories)));
        $this->assertTrue(in_array($another->getId(), array_keys($categories)));


        $department->saveActions([]);
        $department = new Department($this->unusedDepartment);
        $categories = $department->getCategories();
        $this->assertEquals(0, count($categories));
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
