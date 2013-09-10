<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
require_once "PHPUnit/Extensions/Database/TestCase.php";
require_once __DIR__.'/DatabaseTestCase.php';

class CategoryTest extends DatabaseTestCase
{
	private $testGroupId = 1;

	public function getDataSet()
	{
		return $this->createMySQLXMLDataSet(__DIR__.'/categoryTestData.xml');
	}

	public function testSave()
	{
		$name = 'Test Category';

		$category = new Category();
		$category->setName($name);
		$category->setCategoryGroup_id($this->testGroupId);
		$category->save();

		$this->assertEquals($category->getName(), $name);
		$this->assertEquals($category->getCategoryGroup_id(), $this->testGroupId);
	}
}
