<?php
/**
 * @copyright 2013-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Application\ActiveRecord;
use Application\Database;

class GeoCluster extends ActiveRecord
{
    public const CLUSTER_UNIT_SIZE = 0.0001; // 10 Meters (very roughly)
    public const TABLENAME         = 'geoclusters';

    /**
     * Populates the object with data
     *
     * Passing in an associative array of data will populate this object without
     * hitting the database.
     *
     * Passing in a scalar will load the data from the database.
     * This will load all fields in the table as properties of this class.
     * You may want to replace this with, or add your own extra, custom loading
     */
    public function __construct($id=null)
    {
        if ($id) {
            if (is_array($id)) {
                $this->exchangeArray($id);
            }
            else {
                $sql = "select id, level, ST_X(center) as longitude, ST_Y(center) as latitude
                        from geoclusters where id=?";
                $result = Database::query($sql, [$id]);
                if (count($result)) {
                    $this->exchangeArray($result[0]);
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
        $point = ($this->getLatitude() && $this->getLongitude())
                    ? "ST_PointFromText('Point({$this->getLatitude()} {$this->getLongitude()})', 0)"
                    : 'null';

        if ($this->getId()) {
            $sql = "update geoclusters set level=?, center=$point where id=?";
            Database::execute($sql, [$this->getLevel(), $this->getId()]);
        }
        else {
            $pdo = Database::getConnection();
            $sql = "insert geoclusters set level=?, center=$point";
            $ins = $pdo->prepare($sql);
            $ins->execute([$this->getLevel()]);
            $this->data['id'] = $pdo->lastInsertId();
        }
    }

    public function getLevel()     { return   (int)parent::get('level'    ); }
    public function getLatitude()  { return (float)parent::get('latitude' ); }
    public function getLongitude() { return (float)parent::get('longitude'); }

    public static function updateTicketClusters(Ticket $ticket)
    {
        if ($ticket->getId()) {
            $db = Database::getConnection();
            Database::query('delete from ticket_geodata where ticket_id=?', [$ticket->getId()]);

            if ($ticket->getLatitude() && $ticket->getLongitude()) {
                $c = ['ticket_id=:ticket_id'];
                $data['ticket_id'] = $ticket->getId();

                for ($i=0; $i<=6; $i++) {
                    $k   = "cluster_id_$i";
                    $c[] = "$k=:$k";
                    $data[$k] = self::assignClusterIdForLevel($ticket, $i);
                }
                $set = 'set '.implode(',', $c);
                Database::execute("insert into ticket_geodata $set", $data);
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
     */
    public static function assignClusterIdForLevel(Ticket $ticket, int $level)
    {
        $lat   = $ticket->getLatitude ();
        $lng   = $ticket->getLongitude();
        $point = "ST_PointFromText('Point($lat $lng)', 0)";
        $dist  = self::CLUSTER_UNIT_SIZE * pow(2, $level * 2);

        $minX = $lng - $dist;
        $maxX = $lng + $dist;
        $minY = $lat - $dist;
        $maxY = $lat + $dist;
        $bbox = "ST_GeomFromText('Linestring($minX $minY,$maxX $maxY)', 0)";

        $sql  = <<<END
        SELECT id, ST_Distance(center, $point) as distance
        FROM geoclusters
        WHERE level=? and ST_Distance(center, $point) < $dist
        order by distance limit 1
        END;
        $result = Database::query($sql, [$level]);
        if (count($result)) {
            return $result[0]['id'];
        }
        else {
            $cluster = new GeoCluster([
                'level'     => $level,
                'latitude'  => $lat,
                'longitude' => $lng
            ]);
            $cluster->save();
            return $cluster->getId();
        }
    }
}
