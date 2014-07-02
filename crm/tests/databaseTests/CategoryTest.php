<?php
/**
 * @copyright 2013-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\Category;

require_once './DatabaseTestCase.php';

class CategoryTest extends DatabaseTestCase
{
	private $testGroupId      = 1;
	private $testDepartmentId = 1;

	public function getDataSet()
	{
		return $this->createMySQLXMLDataSet(__DIR__.'/testData/categories.xml');
	}

	public function testSave()
	{
		$name = 'Test Category';

		$category = new Category();
		$category->setName($name);
		$category->setCategoryGroup_id($this->testGroupId);
		$category->setDepartment_id($this->testDepartmentId);
		$category->save();

		$this->assertEquals($category->getName(), $name);
		$this->assertEquals($category->getCategoryGroup_id(), $this->testGroupId);
		$this->assertEquals($category->getDepartment_id(), $this->testDepartmentId);
	}
}
