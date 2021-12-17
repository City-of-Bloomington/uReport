<?php
/**
 * @copyright 2012-2021 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Blossom\Classes\Url;

use Solarium\Core\Client\Adapter\Curl;
use Solarium\Exception\ExceptionInterface;
use Solarium\QueryType\Select\Result\Result;
use Solarium\QueryType\Update\Query\Document;
use Solarium\QueryType\Update\Query\Query as UpdateQuery;

use Symfony\Component\EventDispatcher\EventDispatcher;

class Search
{
	public $solr;
	public static $defaultSort = ['enteredDate'=>'desc'];

	const ITEMS_PER_PAGE  = 10;
	const MAX_RAW_RESULTS = 10000;
	const DATE_FORMAT = 'Y-m-d\TH:i:s\Z';

	const SLA_OVERDUE_FUNCTION = '{!frange l=0}if(exists(slaDays),sub(if(exists(closedDate),ms(closedDate,enteredDate),ms(NOW,enteredDate)),product(slaDays,86400000)),-1)';

	/**
	 * The full list of fields that can be searched on
	 *
	 * Security Notice:
	 * Make sure to keep this initial list limited to "safe" fields.
	 * That is: fields that are okay to display to any anonymous person.
	 * Use __construct() to add fields that should be kept hidden
	 * unless a person is authorized to see them.
	 */
	public static $searchableFields = [
		'id',
		'department_id',
		'category_id',
		'client_id',
		'status',
		'substatus_id',
		'addressId',
		'location',
		'city',
		'state',
		'zip',
		'issueType_id',
		'contactMethod_id',
		'enteredDate',
		'bbox',
		'sla'
	];

	/**
	 * These are the base facets
	 *
	 * http://wiki.apache.org/solr/SolrFacetingOverview
	 *
	 * Security Notice:
	 * Make sure to keep this initial list limited to "safe" fields.
	 * That is: fields that are okay to display to any anonymous person.
	 * Use __construct() to add fields that should be kept hidden
	 * unless a person is authorized to see them.
	 */
	public static $facetFields = [
        ['type'=>'field', 'field'=>'category_id'     , 'local_key'=>'category_id'     ],
        ['type'=>'field', 'field'=>'department_id'   , 'local_key'=>'department_id'   ],
        ['type'=>'field', 'field'=>'status'          , 'local_key'=>'status'          ],
        ['type'=>'field', 'field'=>'client_id'       , 'local_key'=>'client_id'       ],
        ['type'=>'field', 'field'=>'issueType_id'    , 'local_key'=>'issueType_id'    ],
        ['type'=>'field', 'field'=>'contactMethod_id', 'local_key'=>'contactMethod_id'],
        ['type'=>'query', 'key'  =>'overdue'         , 'local_key'=>'overdue', 'query'=>self::SLA_OVERDUE_FUNCTION]
	];

	/**
	 * These are the fields that you can sort on
	 *
	 * Many of the fields we search on are ID fields.
	 * Rather than sorting on the ID fields, we really want to
	 * be able to sort on the string values they represent.
	 * Make sure the fields for the string values are indexed
	 */
	public static $sortableFields = [
        'enteredDate',
        'status',
        'location','city','state','zip',
        'department', 'category'
	];

	/**
	 * Connects to Solr and adds additional facet fields
	 */
	public function __construct()
	{
        $this->solr = new \Solarium\Client(
            new Curl(),
            new EventDispatcher(),
            [
                'endpoint' => [
                    'solr' => [
                        'host' => SOLR_SERVER_HOST,
                        'port' => SOLR_SERVER_PORT,
                        'path' => '/',
                        'core' => SOLR_SERVER_CORE
                    ]
                ]
            ]
        );

		// Add facets for the AddressService custom fields
		if (defined('ADDRESS_SERVICE')) {
            $fields = array_keys(call_user_func(ADDRESS_SERVICE.'::customFieldDefinitions'));
            foreach ($fields as $key) {
                self::$searchableFields[] = $key;
                self::$facetFields     [] = ['field'=>$key, 'local_key'=>$key, 'type'=>'field'];
                self::$sortableFields  [] = $key;
            }
        }
		// Add facets that are only to be used if the current user is authorized
		if (Person::isAllowed('people', 'view')) {
			self::$searchableFields[] =  'enteredByPerson_id';
			self::$searchableFields[] =   'assignedPerson_id';
			self::$searchableFields[] = 'reportedByPerson_id';

			self::$facetFields[] = ['field'=>'assignedPerson_id', 'local_key'=>'assignedPerson_id', 'type'=>'field'];

			self::$sortableFields[] = 'enteredByPerson';
			self::$sortableFields[] = 'assignedPerson';
			self::$sortableFields[] = 'reportedByPerson';
		}
	}

	public static function getDefaultFilterQuery(): array
	{
		// User permissions
		if (!isset($_SESSION['USER'])
			|| !in_array($_SESSION['USER']->getRole(), ['Administrator', 'Staff'])) {
			$permissions = 'anonymous';
			if (isset($_SESSION['USER']) && $_SESSION['USER']->getRole()=='Public') {
				$permissions.= ' OR public';
			}
			return [
                ['query'=>"displayPermissionLevel:$permissions"]
            ];
		}
		return [];
	}

	/**
	 * Use the $raw flag to ask for raw results.  This will
	 * disable facetting and pagination.  It will return up to
	 * MAX_RAW_RESULTS.
	 *
	 * @throws \Exception
	 */
	public function query(array $get, ?bool $raw=false): Result
	{
        if (!empty($get['query'])) {
            $get['query'] = trim($get['query']);
            $get['query'] = preg_replace('/[^a-zA-Z0-9\s]/', ' ', $get['query']);
            if (preg_match('|^#?([0-9]+)|', $get['query'], $matches)) {
                $get['id'] = (int)$matches[1];
                unset($get['query']);
            }
        }

		// Start with all the default query values
		$query = !empty($get['query']) ? "{!df=description}$get[query]" : '*:*';
		$sort  = self::$defaultSort;
		$fq    = self::getDefaultFilterQuery();

		// Pagination
		$rows = $raw ? self::MAX_RAW_RESULTS : self::ITEMS_PER_PAGE;
		$startingPage = 0;
		if (!$raw && !empty($get['page'])) {
			$page = (int)$get['page'];
			if ($page < 1) { $page = 1; }

			// Solr rows start at 0, but pages start at 1
			$startingPage = ($page - 1) * $rows;
		}

		// Sorting
		if (isset($get['sort']) && is_array($get['sort'])) {
			$keys = array_keys($get['sort']);
			$k    = $keys[0];
			$dir  = ($get['sort'][$k] == 'asc') ? 'asc' : 'desc';
            $sort = [$k => $dir];
		}

		// Filter Query aka Search Parameters
		foreach (self::$searchableFields as $field) {
			if (substr($field, -3) == '_id' && isset($get[$field])) {
                if (is_numeric(trim($get[$field]))) { $get[$field] = (int)$get[$field]; }
                else { unset($get[$field]); }
			}
			if (!empty($get[$field])) {
				if (false !== strpos($field, 'Date')) {
                    $utc = new \DateTimeZone('UTC');

					if (!empty($get[$field]['start']) || !empty($get[$field]['end'])) {
                        if (!empty(  $get[$field]['start'])) {
                                     $get[$field]['start']->setTimezone($utc);
                            $start = $get[$field]['start']->format(self::DATE_FORMAT);
                        }
                        else { $start = '*'; }


                        if (!empty($get[$field]['end'])) {
                                   $get[$field]['end']->setTimezone($utc);
                            $end = $get[$field]['end']->format(self::DATE_FORMAT);
                        }
                        else { $end = '*'; }

						$fq[] = ['query' => "$field:[$start TO $end]"];
					}
				}
				// coordinates is a not a numeric value but does not need to be quoted.
				elseif (false !== strpos($field, 'bbox')) {
					$key = 'coordinates';
					list($minLat, $minLng, $maxLat, $maxLng) = explode(',', $get[$field]);
					$value = "[$minLat,$minLng TO $maxLat,$maxLng]";
					$fq[] = ['query' => "$key:$value"];
				}
				elseif ($field == 'sla' && $get[$field] == 'overdue') {
                    $fq[] = ['query' => self::SLA_OVERDUE_FUNCTION];
				}
				else {
					$value = is_numeric($get[$field])
						? $get[$field]
						: "\"$get[$field]\"";
					$fq[] = ['query' => "$field:$value"];
				}
			}
		}

        $select = $this->solr->createSelect([
            'query' => $query,
            'start' => $startingPage,
            'rows'  => $rows,
            'sort'  => $sort,
            'filterquery' => $fq,
            'component'   => [
                'facetset' => ['facet' => self::$facetFields]
            ]
        ]);
        try {
            $result = $this->solr->select($select);
        }
        catch (ExceptionInterface $e) {
            $json = json_decode($e->getBody(), true);
            if ($json) { throw new \Exception($json['error']['msg']); }
            else throw($e);
        }
        return $result;
	}

	public function facetValues(string $field): array
	{
        $select = $this->solr->createSelect([
            'query'     => '*:*',
            'rows'      => 1,
            'component' => [
                'facetset' => [
                    'facet' => [['type'=>'field', 'field'=>$field, 'local_key'=>$field]]
                ]
            ]
        ]);
        $result  = $this->solr->select($select);
        $facets  = $result->getFacetSet()->getFacets();
        $values  = array_keys($facets[$field]->getValues());
        ksort($values);
        return $values;
	}

	/**
	 * @return array An array of CRM models based on the search results
	 */
	public static function hydrateDocs(Result $result): array
	{
		$models = [];
		if (count($result)) {
			foreach ($result as $doc) {
				switch ($doc->recordType) {
					case 'ticket':
						// Check to make sure the ticket permits viewing
						// The search engine could be out of sync with the database record
						$t = new Ticket($doc->id);
						if ($t->allowsDisplay(isset($_SESSION['USER']) ? $_SESSION['USER'] : null)) {
							$models[] = new Ticket($doc->id);
						}
						break;
				}
			}
		}
		else {
			header('HTTP/1.1 404 Not Found', true, 404);
		}
		return $models;
	}

	/**
	 * Indexes a single record in Solr
	 *
	 * @param mixed $record
	 */
	public function add(Ticket $ticket)
	{
        $update   = $this->solr->createUpdate();
		$document = $this->createDocument($ticket, $update);
		$update->addDocument($document);
		$update->addCommit();
		$this->solr->update($update);
	}

	/**
	 * Removes a single record from Solr
	 */
	public function delete(Ticket $ticket)
	{
        $update = $this->solr->createUpdate();
        $update->addDeleteById('t_'.$ticket->getId());
        $update->addCommit();
        $this->solr->update($update);
	}

	/**
	 * Prepares a Solr Document with the correct fields for the record type
	 */
	public function createDocument(Ticket $ticket, UpdateQuery $update): Document
	{
		// These are the fields from the tickets table that we're indexing
		//
		// Note: enteredDate, latitude, longitude are indexed as well, even
		// though they are not in this list.
		// They are just handled slightly differently from the generic fields listed
		$ticketFields = [
			'id', 'category_id', 'client_id',
			'enteredByPerson_id', 'assignedPerson_id',
			'addressId', 'location', 'city', 'state', 'zip',
			'status', 'substatus_id',
			'contactMethod_id', 'issueType_id', 'reportedByPerson_id',
			'description'
			// enteredDate, latitude, longitude
		];

        $document = $update->createDocument();
        $document->recordKey  = "t_{$ticket->getId()}";
        $document->recordType = 'ticket';

        $document->enteredDate = $ticket->getEnteredDate(Search::DATE_FORMAT);
        if ($ticket->getClosedDate()) {
            $document->closedDate = $ticket->getClosedDate(Search::DATE_FORMAT);
        }

        if ($ticket->getLatLong()) {
            $document->coordinates = $ticket->getLatLong();
        }
        if ($ticket->getCategory()) {
            $c = $ticket->getCategory();
            $document->displayPermissionLevel = $c->getDisplayPermissionLevel();
            if ($c->getSlaDays()) {
                $document->slaDays = $c->getSlaDays();
            }
        }

        // Ticket information indexing
        foreach ($ticketFields as $f) {
            $get = 'get'.ucfirst($f);
            if ($ticket->$get()) {
                $document->$f = $ticket->$get();
                // For the _id fields, also add a string value
                // ie. category_id=12, category='Graffiti'
                if (substr($f, -3) == '_id') {
                    $o = substr($f, 0, -3);
                    $document->$o = self::sortableString($ticket, $f);
                }
            }
        }
        $person = $ticket->getAssignedPerson();
        if ($person && $person->getDepartment_id()) {
            $document->department_id = $person->getDepartment_id();
            $document->department    = self::sortableString($person, 'department_id');
        }

        // Index extra fields provided by the AddressService
        $additionalFields = $ticket->getAdditionalFields();
        if ($additionalFields) {
            foreach ($additionalFields as $key=>$value) {
                if ($value) {
                    $document->$key = $value;
                }
            }
        }

        if ($ticket->getLatitude() && $ticket->getLongitude()) {
            $latitude  = $ticket->getLatitude();
            $longitude = $ticket->getLongitude();
            $document->latitude  = $latitude;
            $document->longitude = $longitude;

            foreach ($ticket->getClusterIds() as $key=>$value) {
                $document->$key = $value;
            }
        }

        return $document;
	}

	/**
	 * Returns a string for the provided *_id field
	 *
	 * For sorting, we need to index the string values of fields,
	 * even though we're searching on the *_id value
	 *
	 * @param Ticket $record
	 * @param string $field
	 * @return string
	 */
	public static function sortableString($record, $field)
	{
		$n = substr($field, 0, -3);
		$get = 'get'.ucfirst($n);
		$o = $record->$get();
		if (false !== strpos($field, 'Person')) {
			return "{$o->getLastname()} {$o->getFirstname()}";
		}
		else {
			return $o->getName();
		}
	}

	/**
	 * Returns the display name of a CRM object corresponding to a search field
	 *
	 * For each of the self::$searchableFields we need a way to look up the CRM
	 * object corresponding to the value in the search index.
	 * Example: self::getDisplayName('department_id', 32);
	 *
	 * Returns null if the value is an invalid ID
	 */
	public static function getDisplayName(string $fieldname, string $value): ?string
	{
        if (in_array($fieldname, self::$searchableFields)) {
			if (false !== strpos($fieldname, 'Date')) {
				// Reformat Solr date ranges
				// enteredDate:[2011-06-15T00:00:00Z TO 2011-06-30T00:00:00Z]
				preg_match('/\[(.+)\sTO\s(.+)\]/', $value, $matches);
				$start = substr($matches[1], 0, 10);
				$end   = substr($matches[2], 0, 10);
				return "$start - $end";
			}
			elseif (false !== strpos($fieldname, 'Person_id')) {
				try {
					$o = new Person($value);
					return $o->getFullname();
				}
				catch (\Exception $e) {
					// Returns null if person is invalid
				}
			}
			elseif (false !== strpos($fieldname, '_id')) {
				try {
					$class = 'Application\\Models\\'.ucfirst(substr($fieldname, 0, -3));
					$o = new $class($value);
					return $o->getName();
				}
				catch (\Exception $e) {
					// Returns null if the $class ID is invalid
				}
			}
			else {
				return $value;
			}
		}
	}
}
