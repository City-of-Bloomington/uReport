<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\Client;

require_once './DatabaseTestCase.php';

class ClientTest extends DatabaseTestCase
{
	private $testPersonId = 1;

	public function getDataSet()
	{
		return $this->createMySQLXMLDataSet(__DIR__.'/testData/clients.xml');
	}

	public function testSaveAndLoad()
	{
		$name = 'Test Client';
		$client = new Client();
		$client->setName($name);
		$client->setContactPerson_id($this->testPersonId);
		$client->save();

		$id      = $client->getId();
		$api_key = $client->getApi_key();
		$this->assertNotEmpty($id);
		$this->assertNotEmpty($api_key);

		$client = new Client($id);
		$this->assertEquals($api_key, $client->getApi_key());

		$client = Client::loadByApiKey($api_key);
		$this->assertEquals($id, $client->getId());
	}
}
