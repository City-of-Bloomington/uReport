<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
require_once './configuration.inc';
class TestModel extends ActiveRecord
{
	public function validate() { }

	public function getGeneric() { return parent::get('generic'); }
	public function setGeneric($v) { parent::set('generic', $v); }
}

class ActiveRecordTest extends PHPUnit_Framework_TestCase
{
	private $testModel;

	public function __construct()
	{
		$this->testModel = new TestModel();
	}

	public function testGenericGetterSetter()
	{
		$values = array('test', 1);
		foreach ($values as $v) {
			$this->testModel->setGeneric($v);
			$this->assertNotEmpty($this->testModel->getGeneric());
			$this->assertEquals($this->testModel->getGeneric(), $v);
		}
	}
}
