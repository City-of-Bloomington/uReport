<?php
/**
 * @copyright 2012-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Blossom\Classes\Url;

class Search
{
	public $solrClient;
	public static $defaultSort = array('field'=>'enteredDate', 'order'=>'desc');

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
	public static $facetFields = array(
		'ticket'=>array(
			'category_id',
			'department_id',
			'status',
			'client_id',
			'issueType_id',
			'contactMethod_id'
		)
	);

	/**
	 * These are the fields that you can sort on
	 *
	 * Many of the fields we search on are ID fields.
	 * Rather than sorting on the ID fields, we really want to
	 * be able to sort on the string values they represent.
	 * Make sure the fields for the string values are indexed
	 */
	public static $sortableFields = array(
		'ticket'=>array(
			'enteredDate',
			'status',
			'location','city','state','zip',
			'department', 'category'
		)
	);

	/**
	 * Connects to Solr and adds additional facet fields
	 */
	public function __construct()
	{
		$this->solrClient = new \Apache_Solr_Service(
			SOLR_SERVER_HOSTNAME,
			SOLR_SERVER_PORT,
			SOLR_SERVER_PATH,
			false,
			new \Apache_Solr_Compatibility_Solr4CompatibilityLayer()
		);

		// Add facets for the AddressService custom fields
		if (defined('ADDRESS_SERVICE')) {
            $fields = array_keys(call_user_func(ADDRESS_SERVICE.'::customFieldDefinitions'));
            foreach ($fields as $key) {
                self::$searchableFields[]         = $key;
                self::$facetFields   ['ticket'][] = $key;
                self::$sortableFields['ticket'][] = $key;
            }
        }
		// Add facets that are only to be used if the current user is authorized
		if (Person::isAllowed('people', 'view')) {
			self::$searchableFields[] =  'enteredByPerson_id';
			self::$searchableFields[] =   'assignedPerson_id';
			self::$searchableFields[] = 'reportedByPerson_id';

			self::$facetFields['ticket'][] = 'assignedPerson_id';

			self::$sortableFields['ticket'][] = 'enteredByPerson';
			self::$sortableFields['ticket'][] = 'assignedPerson';
			self::$sortableFields['ticket'][] = 'reportedByPerson';
		}
	}

	/**
	 * @return array
	 */
	public static function getDefaultFilterQuery()
	{
		$fq = array();

		// User permissions
		if (!isset($_SESSION['USER'])
			|| !in_array($_SESSION['USER']->getRole(), array('Administrator', 'Staff'))) {
			$permissions = 'anonymous';
			if (isset($_SESSION['USER']) && $_SESSION['USER']->getRole()=='Public') {
				$permissions.= ' OR public';
			}
			$fq[] = "displayPermissionLevel:$permissions";
		}
		return $fq;
	}

	/**
	 * Use the $raw flag to ask for raw results.  This will
	 * disable facetting and pagination.  It will return up to
	 * MAX_RAW_RESULTS.
	 *
	 * @param array $_GET
	 * @param BOOL $raw
	 * @return SolrObject
	 */
	public function query($get, $raw=false)
	{
        if (!empty($get['query'])) {
            $get['query'] = trim($get['query']);
            if (preg_match('|^#?([0-9]+)|', $get['query'], $matches)) {
                $get['id'] = (int)$matches[1];
                unset($get['query']);
            }
        }

		// Start with all the default query values
		$query = !empty($get['query'])
			? "{!df=description}$get[query]"
			: '*:*';
		$sort = self::$defaultSort;
		$fq   = self::getDefaultFilterQuery();

		$additionalParameters = array();

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
		if (isset($get['sort'])) {
			$keys = array_keys($_GET['sort']);
			$sort['field'] = $keys[0];
			$sort['order'] = ($_GET['sort'][$keys[0]] == 'asc')
				? 'asc'
				: 'desc';
		}
		$additionalParameters['sort'] = trim("$sort[field] $sort[order]");

		// Facets
		$additionalParameters['facet'] = 'true';
		$additionalParameters['facet.query'] = self::SLA_OVERDUE_FUNCTION;
		$additionalParameters['facet.field'] = self::$facetFields['ticket'];

		// Search Parameters
		foreach (self::$searchableFields as $field) {
			if (substr($field, -3) == '_id' && isset($get[$field])) {
				$get[$field] = preg_replace('|[^0-9]|', '', $get[$field]);
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

						$fq[] = "$field:[$start TO $end]";
					}
				}
				// coordinates is a not a numeric value but does not need to be quoted.
				elseif (false !== strpos($field, 'bbox')) {
					$key = 'coordinates';
					list($minLat, $minLng, $maxLat, $maxLng) = explode(',', $get[$field]);
					$value = "[$minLat,$minLng TO $maxLat,$maxLng]";
					$fq[] = "$key:$value";
				}
				elseif ($field == 'sla' && $get[$field] == 'overdue') {
                    $fq[] = self::SLA_OVERDUE_FUNCTION;
				}
				else {
					$value = is_numeric($get[$field])
						? $get[$field]
						: "\"$get[$field]\"";
					$fq[] = "$field:$value";
				}
			}
		}

		if (count($fq)) { $additionalParameters['fq'] = $fq; }

		$solrResponse = $this->solrClient->search($query, $startingPage, $rows, $additionalParameters);
		return $solrResponse;
	}

	public function facetValues(string $facet_field): array
	{
        $res = $this->solrClient->search('*:*', 0, 1, [
            'facet'       => 'true',
            'facet.field' => $facet_field
        ]);

        if (          !empty($res->facet_counts->facet_fields->$facet_field)) {
            $facets = (array)$res->facet_counts->facet_fields->$facet_field;
            ksort($facets);
            return $facets;
        }
        return [];
	}

	/**
	 * @param Apache_Solr_Response $object
	 * @return array An array of CRM models based on the search results
	 */
	public static function hydrateDocs(\Apache_Solr_Response $o)
	{
		$models = array();
		if (isset($o->response->docs) && $o->response->docs) {
			foreach ($o->response->docs as $doc) {
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
	public function add($record)
	{
		$document = $this->createDocument($record);
		$this->solrClient->addDocument($document);
	}

	/**
	 * Removes a single record from Solr
	 *
	 * @param mixed $record
	 */
	public function delete($record)
	{
		if ($record instanceof Ticket) {
			$this->solrClient->deleteById('t_'.$record->getId());
		}
	}

	/**
	 * Indexes a whole collection of records in Solr
	 *
	 * @param mixed $list
	 */
	public function index($list)
	{
		foreach ($list as $record) {
			$this->add($record);
		}
	}

	/**
	 * Prepares a Solr Document with the correct fields for the record type
	 *
	 * @param mixed $record
	 * @return Apache_Solr_Document
	 */
	private function createDocument($record)
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

		if ($record instanceof Ticket) {
			$document = new \Apache_Solr_Document();
			$document->addField('recordKey', "t_{$record->getId()}");
			$document->addField('recordType', 'ticket');

			$document->addField('enteredDate',    $record->getEnteredDate(Search::DATE_FORMAT), \DateTimeZone::UTC);
			if ($record->getClosedDate()) {
                $document->addField('closedDate', $record->getClosedDate (Search::DATE_FORMAT), \DateTimeZone::UTC);
            }

			if ($record->getLatLong()) {
				$document->addField('coordinates', $record->getLatLong());
			}
			if ($record->getCategory()) {
                $c = $record->getCategory();
				$document->addField('displayPermissionLevel', $c->getDisplayPermissionLevel());
				if ($c->getSlaDays()) {
                    $document->addField('slaDays', $c->getSlaDays());
				}
			}

			// Ticket information indexing
			foreach ($ticketFields as $f) {
				$get = 'get'.ucfirst($f);
				if ($record->$get()) {
					$document->addField($f, $record->$get());
					if (substr($f, -3) == '_id') {
						$document->addField(substr($f, 0, -3), self::sortableString($record, $f));
					}
				}
			}
			$person = $record->getAssignedPerson();
			if ($person && $person->getDepartment_id()) {
				$document->addField('department_id', $person->getDepartment_id());
				$document->addField('department', self::sortableString($person, 'department_id'));
			}

			// Index extra fields provided by the AddressService
			$additionalFields = $record->getAdditionalFields();
			if ($additionalFields) {
				foreach ($additionalFields as $key=>$value) {
					if ($value) {
						$document->addField($key, $value);
					}
				}
			}

			if ($record->getLatitude() && $record->getLongitude()) {
				$latitude  = $record->getLatitude();
				$longitude = $record->getLongitude();
				$document->addField('latitude' , $latitude );
				$document->addField('longitude', $longitude);

				foreach ($record->getClusterIds() as $key=>$value) {
					$document->addField($key, $value);
				}
			}

			return $document;
		}
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
	 *
	 * @param string $fieldname
	 * @param string $value
	 * @return string
	 */
	public static function getDisplayName($fieldname, $value)
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

	/**
	 * @param Apache_Solr_Response $solrObject
	 * @return array
	 */
	public static function getCurrentFilters(\Apache_Solr_Response $solrObject)
	{
        $currentFilters = [];

        if (isset($solrObject->responseHeader->params->fq)) {
            $fq = $solrObject->responseHeader->params->fq;

            if ($fq) {
                // It might happen that there is only one filterQuery
                if (!is_array($fq)) { $fq = [$fq]; }


                $currentUrl = new Url(Url::current_url(BASE_HOST));
                foreach ($fq as $filter) {
                    $deleteUrl = clone $currentUrl;

                    if (preg_match('/([^:]+):(.+)/', $filter, $matches)) {
                        $key   = $matches[1];
                        $value = $matches[2];

                        // String values come back with double quotes around them.
                        // We need to strip the quotes to get the raw values.
                        if (substr($value, 0, 1) === '"' && substr($value, -1) === '"') {
                            $value = substr($value, 1, -1);
                        }

                        if (substr($key, -3) === '_id') {
                            $value = self::getDisplayName($key, $value);
                        }

                        // The input and output syntax for bounding box definitions are different
                        // The query gets sent to search using "bbox"; however,
                        // when the parameters come back from SOLR, the "bbox" has been
                        // renamed to "coordinates".
                        if ($key === 'coordinates') {
                            $key = 'bbox';
                            if (isset($deleteUrl->zoom)) { unset($deleteUrl->zoom); }
                        }
                    }
                    else {
                        if ($filter == Search::SLA_OVERDUE_FUNCTION) {
                            $key   = 'sla';
                            $value = 'overdue';
                        }
                    }

                    if (in_array($key, Search::$searchableFields)) {
                        if (isset($deleteUrl->$key)) { unset($deleteUrl->$key); }

                        $currentFilters[$key] = ['value'=>$value, 'deleteUrl'=>$deleteUrl->__toString()];
                    }
                }
            }
        }
        return $currentFilters;
	}
}
