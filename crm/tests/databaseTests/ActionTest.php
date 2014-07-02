<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\Action;

require_once './DatabaseTestCase.php';

class ActionTest extends DatabaseTestCase
{
	public function getDataSet()
	{
		return $this->createMySQLXMLDataSet(__DIR__.'/testData/actions.xml');
	}

	public function testSaveAndLoad()
	{
		$action = new Action();
		$action->setName('Test');
		$action->setDescription('Description');
		$action->save();

		$id = $action->getId();
		$this->assertNotEmpty($id);

		$action = new Action($id);
		$this->assertEquals('Test', $action->getName());
		$this->assertEquals('Description', $action->getDescription());
	}
}
