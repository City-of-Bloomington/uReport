<?php
/**
 * @copyright 2013-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;
use Application\ActiveRecord;
use Application\Database;
use Laminas\Db\Sql\Sql;

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
				$db = Database::getConnection();
				$sql = "select id, level, ST_X(center) as longitude, ST_Y(center) as latitude
						from geoclusters where id=?";
                $result = $db->createStatement($sql)->execute([$id]);
                if (count($result)) {
                    $this->exchangeArray($result->current());
                }
				else {
					throw new \Exception('geoclusters/unknown');
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
		$db    = Database::getConnection();
		$point = ($this->getLatitude() && $this->getLongitude())
                    ? "ST_PointFromText('Point({$this->getLatitude()} {$this->getLongitude()})', 4326)"
                    : 'null';

		if ($this->getId()) {
			$sql = "update geoclusters set level=?, center=$point where id=?";
			$db->query($sql)->execute([$this->getLevel(), $this->getId()]);
		}
		else {
			$sql = "insert geoclusters set level=?, center=$point";
			$db->query($sql)->execute([$this->getLevel()]);
			$this->data['id'] = $db->getDriver()->getLastGeneratedValue();
		}
	}

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()        { return   (int)parent::get('id'       ); }
	public function getLevel()     { return   (int)parent::get('level'    ); }
	public function getLatitude()  { return (float)parent::get('latitude' ); }
	public function getLongitude() { return (float)parent::get('longitude'); }

	public function setLatitude ($f) { parent::set('latitude' , (float)$f); }
	public function setLongitude($f) { parent::set('longitude', (float)$f); }
	public function setLevel($i=null) { $this->data['level'] = isset($i) ? (int)$i : null; }

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	public static function updateTicketClusters(Ticket $ticket)
	{
		if ($ticket->getId()) {
			$db = Database::getConnection();
			$db->query('delete from ticket_geodata where ticket_id=?')->execute([$ticket->getId()]);

			if ($ticket->getLatitude() && $ticket->getLongitude()) {
				$data['ticket_id'] = $ticket->getId();
				for ($i=0; $i<=6; $i++) {
					$data["cluster_id_$i"] = self::assignClusterIdForLevel($ticket, $i);
				}

				$sql = new Sql($db);
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
		$lat   = $ticket->getLatitude ();
		$lng   = $ticket->getLongitude();
		$point = "ST_PointFromText('Point($lat $lng)', 4326)";
		$dist  = 0.01 * pow(2, $level * 2); // Kilometers

		$minX = $lng - $dist;
		$maxX = $lng + $dist;
		$minY = $lat - $dist;
		$maxY = $lat + $dist;
		$bbox = "ST_GeomFromText('Linestring($minX $minY,$maxX $maxY)', 4326)";

		// Geocluster center points are in the database, so we can just look
		// them up. However, MySQL spatial functions only allow bounding box
		// queries, not points inside a circle, which is what we want.
		// So, here, we're still calculating the haversine distance
		$sql  = "
		SELECT id,
               ST_X(center) as longitude,
               ST_Y(center) as latitude,
               ST_Distance(center, $point, 'kilometre') as distance
		FROM geoclusters
		WHERE level=?
		  and MBRWithin(center, $bbox)
		order by distance
		LIMIT 1
		";
		$db = Database::getConnection();
		$result = $db->query($sql)->execute([$level]);
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
