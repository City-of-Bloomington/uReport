<?php
/**
 * @copyright 2013-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\GeoCluster;
use Application\Models\Ticket;
use Blossom\Classes\Database;

require_once './DatabaseTestCase.php';

class GeoClusterTest extends DatabaseTestCase
{
	private $testTicketId  = 1;
	private $testLatitude  = 39.169927;
	private $testLongitude = -86.536806;

	public function getDataSet()
	{
		return $this->createMySQLXMLDataSet(__DIR__.'/testData/geoclusters.xml');
	}

	public function testSave()
	{
		$cluster = new GeoCluster();
		$cluster->setLevel(0);
		$cluster->setLatitude ($this->testLatitude );
		$cluster->setLongitude($this->testLongitude);
		$cluster->save();

		$id = $cluster->getId();
		$this->assertGreaterThan(0, $id);

		$cluster = new GeoCluster($id);
		$this->assertEquals($this->testLatitude , $cluster->getLatitude ());
		$this->assertEquals($this->testLongitude, $cluster->getLongitude());
	}

	public function testAssignClusterId()
	{
		$ticket = new Ticket($this->testTicketId);
		$data = array();
		for ($i=0; $i<=6; $i++) {
			$data["cluster_id_$i"] = GeoCluster::assignClusterIdForLevel($ticket, $i);
			$this->assertGreaterThan(0, $data["cluster_id_$i"]);
		}
	}

	public function testUpdateTicketClusters()
	{
		$ticket = new Ticket($this->testTicketId);
		GeoCluster::updateTicketClusters($ticket);

		$zend_db = Database::getConnection();
		$result = $zend_db->query('select * from ticket_geodata where ticket_id=?')->execute([$this->testTicketId]);
		$row = $result->current();
		for ($i=0; $i<=6; $i++) {
			$this->assertGreaterThan(0, $row["cluster_id_$i"]);
		}
	}
}
