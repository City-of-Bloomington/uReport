<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\Address;
use Application\Models\Person;

require_once './DatabaseTestCase.php';

class AddressTest extends DatabaseTestCase
{
	private $testPersonId = 1;

	public function getDataSet()
	{
		return $this->createMySQLXMLDataSet(__DIR__.'/testData/addresses.xml');
	}

	public function testSetPerson()
	{
		$person = new Person($this->testPersonId);
		$this->assertEquals($this->testPersonId, $person->getId());

		$address = new Address();
		$address->setPerson($person);
		$this->assertEquals($this->testPersonId, $address->getPerson_id());
	}

	public function testSaveAndLoad()
	{
		$person = new Person($this->testPersonId);
		$this->assertEquals($this->testPersonId, $person->getId());

		$address = new Address();
		$address->setAddress('test');
		$address->setPerson($person);
		$address->save();

		$id = $address->getId();
		$this->assertNotEmpty($id);

		$address = new Address($id);
		$this->assertEquals('test', $address->getAddress());
		$this->assertEquals(1, $address->getPerson_id());
	}
}
