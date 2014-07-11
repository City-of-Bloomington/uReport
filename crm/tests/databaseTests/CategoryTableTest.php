<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\CategoryTable;
use Application\Models\Person;

require_once './DatabaseTestCase.php';

class CategoryTableTest extends DatabaseTestCase
{
	private $inUseDepartment  = 1;
	private $unusedDepartment = 2;

	public function getDataSet()
	{
		return $this->createMySQLXMLDataSet(__DIR__.'/testData/categoryTable.xml');
	}

	public function testPermissions()
	{
		$table = new CategoryTable();
		$list = $table->find();
		$this->assertEquals(1, count($list));

		$person = new Person();
		$person->setRole('Staff');

		$list = $table->find(['displayableTo'=>$person]);
		$this->assertEquals(3, count($list));

		$_SESSION['USER'] = $person;
		$list = $table->find();
		$this->assertEquals(3, count($list));
	}

	public function testGetByDepartment()
	{
		$table = new CategoryTable();
		$list = $table->find(['department_id'=>$this->unusedDepartment]);
		$this->assertEquals(2, count($list));
	}
}
