<?php
/**
 * @copyright 2013-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;
use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;
use Zend\Db\Sql\Sql;

class GeoCluster extends ActiveRecord
{
	protected $tablename = 'geoclusters';
	/**
	 * Populates the object with data
	 *
	 * Passing in an associative array of data will populate this object without
	 * hitting the database.
	 *
	 * Passing in a scalar will load the data from the database.
	 * This will load all fields in the table as properties of this class.
	 * You may want to replace this with, or add your own extra, custom loading
	 *
	 * @param int|array $id
	 */
	public function __construct($id=null)
	{
		if ($id) {
			if (is_array($id)) {
                $this->exchangeArray($id);
			}
			else {
				$zend_db = Database::getConnection();
				$sql = "select id,level,x(center) as longitude, y(center) as latitude
						from geoclusters where id=?";
                $result = $zend_db->createStatement($sql)->execute([$id]);
                if (count($result)) {
                    $this->exchangeArray($result->current());
                }
				else {
					throw new \Exception('geoclusters/unknownCluster');
				}
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
		}
	}

	public function validate()
	{
		if (!isset($this->data['level'])
			|| !$this->getLatitude() || !$this->getLongitude()) {
			throw new \Exception('missingRequiredFields');
		}
	}

	public function save()
	{
		 // We cannot use the default ActiveRecord save,
		 // because the center point must use mysql spatial functions
		$this->validate();
		$zend_db = Database::getConnection();
		if ($this->getId()) {
			$sql = 'update geoclusters set level=?, center=point(?, ?) where id=?';
			$zend_db->query($sql)->execute([
				$this->getLevel(), $this->getLongitude(), $this->getLatitude(), $this->getId()
			]);
		}
		else {
			$sql = 'insert geoclusters set level=?, center=point(?, ?)';
			$zend_db->query($sql)->execute([
				$this->getLevel(), $this->getLongitude(), $this->getLatitude()
			]);
			$this->data['id'] = $zend_db->getDriver()->getLastGeneratedValue();
		}
	}

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()        { return parent::get('id'       ); }
	public function getLevel()     { return parent::get('level'    ); }
	public function getLatitude()  { return parent::get('latitude' ); }
	public function getLongitude() { return parent::get('longitude'); }

	public function setLatitude ($f) { parent::set('latitude' , (float)$f); }
	public function setLongitude($f) { parent::set('longitude', (float)$f); }
	public function setLevel($i=null) { $this->data['level'] = isset($i) ? (int)$i : null; }

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	public static function updateTicketClusters(Ticket $ticket)
	{
		if ($ticket->getId()) {
			$zend_db = Database::getConnection();
			$zend_db->query('delete from ticket_geodata where ticket_id=?')->execute([$ticket->getId()]);

			if ($ticket->getLatitude() && $ticket->getLongitude()) {
				$data['ticket_id'] = $ticket->getId();
				for ($i=0; $i<=6; $i++) {
					$data["cluster_id_$i"] = self::assignClusterIdForLevel($ticket, $i);
				}

				$sql = new Sql($zend_db);
				$insert = $sql->insert('ticket_geodata');
				$insert->values($data);
				$sql->prepareStatementForSqlObject($insert)->execute();
			}
		}
	}

	/**
	 * Finds or creates a cluster for the given Ticket
	 *
	 * If there is already a cluster within a certain distance,
	 * this will return the ID for that cluster.
	 * If it doesn't find a nearby cluster, this will add a
	 * new cluster to the database and return the new ID.
	 *
	 * @param Ticket $ticket
	 * @param int $level
	 */
	public static function assignClusterIdForLevel(Ticket $ticket, $level)
	{
		$lat  = $ticket->getLatitude ();
		$lng  = $ticket->getLongitude();
		$dist = 0.01 * pow(2, $level * 2);

		$minX = $lng - $dist;
		$maxX = $lng + $dist;
		$minY = $lat - $dist;
		$maxY = $lat + $dist;

		// Geocluster center points are in the database, so we can just look
		// them up. However, MySQL spatial functions only allow bounding box
		// queries, not points inside a circle, which is what we want.
		// So, here, we're still calculating the haversine distance
		$sql  = "
		SELECT id,x(center) as longitude, y(center) as latitude,
			( SELECT
				(ACOS(SIN(RADIANS(y(center))) * SIN(RADIANS($lat))
				    + COS(RADIANS(y(center))) * COS(RADIANS($lat))
				    * COS(RADIANS(x(center) - $lng))) * 6371.0)
			) as distance
		FROM geoclusters
		WHERE level=?
		  and (ACOS(SIN(RADIANS(y(center))) * SIN(RADIANS($lat))
			      + COS(RADIANS(y(center))) * COS(RADIANS($lat))
			      * COS(RADIANS(x(center) - $lng))) * 6371.0) < $dist
		order by distance
		LIMIT 1
		";
		$zend_db = Database::getConnection();
		$result = $zend_db->query($sql)->execute([$level]);
		if (count($result)) {
			$row = $result->current();
			return $row['id'];
		}
		else {
			$cluster = new GeoCluster();
			$cluster->setLevel($level);
			$cluster->setLatitude ($lat);
			$cluster->setLongitude($lng);
			$cluster->save();
			return $cluster->getId();
		}
	}
}
