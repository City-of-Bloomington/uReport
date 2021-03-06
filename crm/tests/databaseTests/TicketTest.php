<?php
/**
 * @copyright 2013-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
use Application\Models\Ticket;
use Blossom\Classes\Database;

require_once './DatabaseTestCase.php';

class TicketTest extends DatabaseTestCase
{
	private $testCategoryId = 1;
	private $testLatitude  = 39.169927;
	private $testLongitude = -86.536806;

	public function getDataSet()
	{
		return $this->createMySQLXMLDataSet(__DIR__.'/testData/tickets.xml');
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

		$db     = Database::getConnection();
		$result = $db->query('select * from ticket_geodata where ticket_id=?')->execute([$id]);
		$row    = $result->current();
		for ($i=0; $i<=6; $i++) {
			$this->assertGreaterThan(0, $row["cluster_id_$i"]);
		}
	}

	public function testLatLngShouldNotAllowZeros()
	{
		$ticket = new Ticket();
		$ticket->handleAdd(array(
			'description'=> 'Testing',
			'category_id'=> $this->testCategoryId,
			'latitude'   => 0,
			'longitude'  => 0
		));
		$id     = $ticket->getId();
		$db     = Database::getConnection();
		$result = $db->query('select latitude,longitude from tickets where id=?')->execute([$id]);
		$row    = $result->current();
		$this->assertNull($row['latitude' ]);
		$this->assertNull($row['longitude']);
	}

	public function testDefaultSubstatus()
	{
		$s1 = new Substatus('Test One');
		$s2 = new Substatus('Test Two');

		$ticket = new Ticket();
		$this->assertEquals('Test One', $ticket->getSubstatus()->getName());

		$s2->setDefault(true);
		$s2->save();

		$ticket = new Ticket();
		$this->assertEquals('Test Two', $ticket->getSubstatus()->getName());
	}
}
