<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\ContactMethod;

require_once './DatabaseTestCase.php';

class ContactMethodTest extends DatabaseTestCase
{
	public function getDataSet()
	{
		return $this->createMySQLXMLDataSet(__DIR__.'/testData/contactMethods.xml');
	}

	public function testSaveAndLoad()
	{
		$method = new ContactMethod();
		$method->setName('test');
		$method->save();

		$id = $method->getId();
		$this->assertNotEmpty($id);

		$method = new ContactMethod($id);
		$this->assertEquals('test', $method->getName());
	}
}
