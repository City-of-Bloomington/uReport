<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Search
{
	public $solrClient;
	public static $defaultSort = array('field'=>'enteredDate', 'order'=>SolrQuery::ORDER_DESC);

	const ITEMS_PER_PAGE = 10;
	const DATE_FORMAT = 'Y-m-d\TH:i:s\Z';

	/**
	 * These are the base facets all users are allowed to search on
	 *
	 * The order declared here is the same order these facets will be
	 * displayed, when used.
	 *
	 * Security Notice:
	 * Make sure to keep this initial list limited to "safe" fields.
	 * That is: fields that are okay to display to any anonymous person.
	 * Use __construct() to add fields that should be kept hidden
	 * unless a person is authorized to see them.
	 */
	public static $facetFields = array(
		'ticket'=>array(
			'category_id'  => 'Category',
			'department_id'=> 'Department',
			'status'       => 'Status',
			'client_id'    => 'Client',
			'label_id'     => 'Label',
			'issueType_id' => 'IssueType',
		)
	);

	/**
	 * Connects to Solr and adds additional facet fields
	 */
	public function __construct()
	{
		$this->solrClient = new SolrClient(array(
			'hostname'=> SOLR_SERVER_HOSTNAME,
			'port'    => SOLR_SERVER_PORT,
			'path'    => SOLR_SERVER_PATH
		));

		// Add facets for the AddressService custom fields
		foreach (AddressService::$customFieldDescriptions as $key=>$desc) {
			self::$facetFields['ticket'][$key] = $desc['description'];
		}
		// Add facets that are only to be used if the current user is authorized
		if (userIsAllowed('people', 'view')) {
			self::$facetFields['ticket']['assignedPerson_id'] = 'Assigned To';
		}
	}

	/**
	 * @param array $_GET
	 * @return SolrObject
	 */
	public function query($get)
	{
		$query = new SolrQuery('*:*');
		$query->setFacet(true);
		$query->setRows(self::ITEMS_PER_PAGE);
		if (!empty($get['page'])) {
			$page = (int)$get['page'];
			if ($page < 1) { $page = 1; }
			$query->setStart($page * self::ITEMS_PER_PAGE);
		}

		$sort = self::$defaultSort;
		if (isset($get['sort'])) {
			$keys = array_keys($_GET['sort']);
			$sort['field'] = $keys[0];
			$sort['order'] = ($_GET['sort'][$keys[0]] == SolrQuery::ORDER_ASC)
				? SolrQuery::ORDER_ASC
				: SolrQuery::ORDER_DESC;
		}
		$query->addSortField($sort['field'], $sort['order']);

		foreach (self::$facetFields['ticket'] as $field=>$displayName) {
			$query->addFacetField($field);
			if (!empty($get[$field])) {
				$query->addFilterQuery("$field:$get[$field]");
			}
		}
		$solrResponse = $this->solrClient->query($query);
		return $solrResponse->getResponse();
	}

	/**
	 * @param SolrObject $object
	 * @return array An array of CRM models based on the search results
	 */
	public static function hydrateDocs(SolrObject $o)
	{
		$models = array();
		if (isset($o->response->docs) && $o->response->numFound) {
			foreach ($o->response->docs as $doc) {
				switch ($doc->recordType) {
					case 'ticket':
						$models[] = new Ticket($doc->id);
						break;
				}
			}
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
	 * @return SolrInputDocument
	 */
	private function createDocument($record)
	{
		if ($record instanceof Ticket) {
			$document = new SolrInputDocument();
			$document->addField('recordKey', "t_{$record->getId()}");
			$document->addField('recordType', 'ticket');
			$document->addField('enteredDate', $record->getEnteredDate(Search::DATE_FORMAT), DateTimeZone::UTC);
			if ($record->getLatLong()) {
				$document->addField('coordinates', $record->getLatLong());
			}

			$fields = array(
				'id', 'category_id', 'client_id',
				'enteredByPerson_id', 'assignedPerson_id', 'referredPerson_id',
				'address_id', 'location', 'city', 'state', 'zip',
				'status', 'resolution_id'
			);
			foreach ($fields as $f) {
				$get = 'get'.ucfirst($f);
				if ($record->$get()) {
					$document->addField($f, $record->$get());
				}
			}
			$person = $record->getAssignedPerson();
			if ($person && $person->getDepartment_id()) {
				$document->addField('department_id', $person->getDepartment_id());
			}

			$issueFields = array(
				'contactMethod_id', 'responseMethod_id', 'issueType_id', 'reportedByPerson_id'
			);
			$description = '';
			foreach ($record->getIssues() as $issue) {
				$description.= $issue->getDescription();
				foreach ($issueFields as $field) {
					$get = 'get'.ucfirst($field);
					if ($issue->$get()) {
						$document->addField($field, $issue->$get());
					}
				}
				foreach ($issue->getLabels() as $label) {
					$document->addField('label_id', $label);
				}
			}
			if ($description) {
				$document->addField('description', $description);
			}

			foreach (AddressService::$customFieldDescriptions as $field=>$def) {
				$value = $record->get($field);
				if ($value) {
					$document->addField($field, $value);
				}
			}

			return $document;
		}
	}

	/**
	 * Returns the display name of a CRM object corresponding to a facet
	 *
	 * For each of the self::$facetFields we need a way to look up the CRM
	 * object corresponding to the facet values in the search index.
	 * Example: self::getDisplayName('ticket', 'department_id', '4e08f7f0992b949b72000022');
	 *
	 * @param string $recordType
	 * @param string $facetFieldKey
	 * @param string $value
	 */
	public static function getDisplayName($recordType, $facetFieldKey, $value)
	{
		if (isset(self::$facetFields[$recordType][$facetFieldKey])) {
			if (preg_match('/Person_id$/', $facetFieldKey)) {
				$o = new Person($value);
				return $o->getFullname();
			}
			elseif (preg_match('/_id$/', $facetFieldKey)) {
				$class = self::$facetFields[$recordType][$facetFieldKey];
				$o = new $class($value);
				return $o->getName();
			}
			else {
				return $value;
			}
		}
	}

	public static function getSortableFields($recordType='ticket')
	{
		$fields = array(
			'enteredDate',
			'status',
			'location','city','state','zip'
		);
		foreach (AddressService::$customFieldDescriptions as $key=>$value) {
			$fields[] = $key;
		}
		return $fields;
	}
}