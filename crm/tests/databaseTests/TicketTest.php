<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
require_once "PHPUnit/Extensions/Database/TestCase.php";
require_once __DIR__.'/DatabaseTestCase.php';

class TicketTest extends DatabaseTestCase
{
	private $testCategoryId = 1;
	private $testLatitude  = 39.169927;
	private $testLongitude = -86.536806;

	public function getDataSet()
	{
		return $this->createMySQLXMLDataSet(__DIR__.'/ticketTestData.xml');
	}

	public function testAdd()
	{
		$ticket = new Ticket();
		$ticket->handleAdd(array(
			'description'=>'Testing',
			'category_id'=>$this->testCategoryId
		));
		$id = $ticket->getId();

		$this->assertGreaterThan(0, $id);

		$ticket = new Ticket($id);
		$this->assertEquals($ticket->getCategory_id(), $this->testCategoryId);
	}

	public function testSaveLatLong()
	{
		$ticket = new Ticket();
		$ticket->handleAdd(array(
			'category_id'=> $this->testCategoryId,
			'latitude'   => $this->testLatitude,
			'longitude'  => $this->testLongitude
		));
		$id = $ticket->getId();

		$this->assertGreaterThan(0, $id);
		$this->assertEquals($ticket->getLatitude() , $this->testLatitude );
		$this->assertEquals($ticket->getLongitude(), $this->testLongitude);
	}
}
