<?php
/**
 * @copyright 2011-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Application\ActiveRecord;
use Application\PdoRepository;

class TicketTable extends PdoRepository
{
    public const TABLENAME = 'tickets';
    public const CLASSNAME = __namespace__.'\Ticket';

    /**
     * The set of fields we want to display in search results by default
     */
    public static $defaultFieldsToDisplay = [
        'enteredDate', 'location', 'description', 'category_id', 'status'
    ];

    public function find(array $fields=[], ?string $order='t.enteredDate desc', ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $select =  'select t.* from tickets t';
        $joins  = ['left join categories c on c.id=t.category_id'];
        $where  = [];
        $params = [];

        if ($fields) {
            foreach ($fields as $k=>$v) {
                if ($v) {
                    switch ($k) {
                        case 'start_date':
                        case 'end_date':
                        case 'lastModified_before':
                        case 'lastModified_after':
                            if (get_class($v) !== 'DateTime') { throw new \Exception('invalidDate'); }
                            $datetime = $v->format(ActiveRecord::MYSQL_DATETIME_FORMAT);

                            switch ($k) {
                                case          'start_date': $where[] = "t.enteredDate  >= '$datetime'"; break;
                                case            'end_date': $where[] = "t.enteredDate  <= '$datetime'"; break;
                                case 'lastModified_before': $where[] = "t.lastModified <= '$datetime'"; break;
                                case  'lastModified_after': $where[] = "t.lastModified >= '$datetime'"; break;
                            }
                        break;

                        case 'bbox':
                            $bbox = explode(',', $v);
                            if (count($bbox) == 4) {
                                $minLat  = (float)$bbox[0];
                                $minLong = (float)$bbox[1];
                                $maxLat  = (float)$bbox[2];
                                $maxLong = (float)$bbox[3];
                                $where[] = 'tickets.latitude is not null and tickets.longitude is not null';
                                $where[] = "tickets.latitude  > $minLat";
                                $where[] = "tickets.longitude > $minLong";
                                $where[] = "tickets.latitude  < $maxLat";
                                $where[] = "tickets.longitude < $maxLong";
                            }

                            break;
                        default:
                            $where[] = "t.$k=:$k";
                            $params[$k] = $v;
                    }
                }
            }
        }
        // Only get tickets for categories this user is allowed to see
        if (!isset($_SESSION['USER'])) {
            $where[] = "c.displayPermissionLevel='anonymous'";
        }
        elseif (   $_SESSION['USER']->getRole()!='Staff'
                && $_SESSION['USER']->getRole()!='Administrator') {
            $where[] = "c.displayPermissionLevel in ('public','anonymous')";
        }

        $sql  = parent::buildSql($select, $joins, $where, null, $order);
        return  parent::performSelect($sql, $params, $itemsPerPage, $currentPage);
    }

    /**
     * Returns fields that can be displayed in ticketList and searchResults
     *
     * The key should be the fieldname in the database
     * The value should be a nice, readable label for the field
     */
    public static function getDisplayableFields(): array
    {
        $fields = [
            'id'                => 'Case #',
            'enteredDate'       => 'Case Date',
            'enteredByPerson_id'=> 'Entered By',
            'assignedPerson_id' => 'Assigned To',
            'category_id'       => 'Category',
            'department_id'     => 'Department',
            'status'            => 'Status',
            'substatus_id'      => 'Substatus',
            'location'          => 'Location',
            'latitude'          => 'Latitude',
            'longitude'         => 'Longitude',
            'city'              => 'City',
            'state'             => 'State',
            'zip'               => 'Zip',
            'description'       => 'Description',
            'slaPercentage'     => 'SLA'
        ];
        if (defined('ADDRESS_SERVICE')) {
            $custom = call_user_func(ADDRESS_SERVICE.'::customFieldDefinitions');
            foreach ($custom as $key=>$def) {
                $fields[$key] = $def['description'];
            }
        }
        return $fields;
    }

    /**
     * Returns fields this class knows how to search for
     *
     * These should match the fieldnames in the database
     */
    public static function getSearchableFields(): array
    {
        $fields = [
            'enteredDate', 'description',
            'enteredByPerson_id', 'assignedPerson_id', 'reportedByPerson_id',
            'category_id', 'department_id', 'client_id',
            'status', 'substatus_id',
            'location', 'latitude', 'longitude',
            'city', 'state', 'zip',
            'start_date',
            'end_date',
            'label_id', 'issueType_id'
        ];
        if (defined('ADDRESS_SERVICE')) {
            $fields = array_keys(call_user_func(ADDRESS_SERVICE.'::customFieldDefinitions'));
            foreach ($fields as $key) {
                $fields[$key] = $key;
            }
        }
        return $fields;
    }

    /**
     * Tells whether the given request has fields that are searchable
     *
     * @param array $request Usually the raw $_REQUEST
     * @return bool
     */
    public static function isValidSearch($request)
    {
        return count(array_intersect(
            array_keys(self::getSearchableFields()),
            array_keys($request)
        )) ? true : false;
    }

    /**
     * Prepares a bounding box for finding nearby tickets
     * @see self::find()
     */
    public static function nearbyBoundingBox(float $lat, float $lon): array
    {
        return [
            $lat - 0.0001,
            $lon - 0.0001,
            $lat + 0.0001,
            $lon + 0.0001
        ];
    }
}
