<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
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
				$result = $id;
			}
			else {
				$zend_db = Database::getConnection();
				$sql = "select id,level,x(center) as longitude, y(center) as latitude
						from geoclusters where id=?";
				$result = $zend_db->fetchRow($sql, array($id));
			}
			if ($result) {
				$this->data = $result;
			}
			else {
				throw new Exception('geoclusters/unknownCluster');
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
			throw new Exception('missingRequiredFields');
		}
	}

	public function save()
	{
		 // We cannot use the default ActiveRecord save,
		 // because the center point must use mysql spatial functions
		$this->validate();
		$data = array(
			'level' => $this->data['level'],
			'center'=> new Zend_Db_Expr("point({$this->data['longitude']}, {$this->data['latitude']})")
		);
		$zend_db = Database::getConnection();
		if ($this->getId()) {
			$data['id'] = $this->data['id'];
			$zend_db->update($this->tablename, $data, "id={$this->data['id']}");
		}
		else {
			$zend_db->insert($this->tablename, $data);
			$this->data['id'] = $zend_db->lastInsertId($this->tablename, 'id');
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
			$zend_db->delete('ticket_geodata', 'ticket_id='.$ticket->getId());

			if ($ticket->getLatitude() && $ticket->getLongitude()) {
				$data['ticket_id'] = $ticket->getId();
				for ($i=0; $i<=6; $i++) {
					$data["cluster_id_$i"] = self::assignClusterIdForLevel($ticket, $i);
				}

				$zend_db->insert('ticket_geodata', $data);
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
		$row = $zend_db->fetchRow($sql, $level);
		if ($row) {
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
