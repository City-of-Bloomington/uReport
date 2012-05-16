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
	 */
	public static $facetFields = array(
		'ticket'=>array(
			'category_id'=>'Category',
			'department_id'=>'Department',
			'status'=>'Status',
			'client_id'=>'Client',
			'label'=>'Label',
			'type'=>'Type',
		)
	);

	public function __construct()
	{
		$this->solrClient = new SolrClient(array(
			'hostname'=> SOLR_SERVER_HOSTNAME,
			'port'    => SOLR_SERVER_PORT,
			'path'    => SOLR_SERVER_PATH
		));

		// Add additional fields to the faceted browsing
		foreach (AddressService::$customFieldDescriptions as $key=>$desc) {
			self::$facetFields['ticket'][$key] = $desc['description'];
		}
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
	public static function hydrateDocs(SolrObject $object)
	{
		$models = array();
		if (isset($object->response->docs)) {
			foreach ($object->response->docs as $doc) {
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
			$document->addField('recordType', 'ticket');
			$document->addField('id', (string)$record->getId());
			$document->addField('enteredDate', $record->getEnteredDate(Search::DATE_FORMAT), DateTimeZone::UTC);
			if ($record->getLatLong()) {
				$document->addField('coordinates', $record->getLatLong());
			}
			if ($record->getCategory()) {
				$document->addField('category_id', "{$record->getCategory()->getId()}");
			}

			$stringFields = array(
				'status','resolution','client_id',
				'location','city','state','zip'
			);
			foreach ($stringFields as $field) {
				$get = 'get'.ucfirst($field);
				if ($record->$get()) {
					$document->addField($field, $record->$get());
				}
			}

			$personFields = array('enteredBy','assigned','referred');
			foreach ($personFields as $field) {
				$get = 'get'.ucfirst($field).'Person';
				$person = $record->$get();
				if ($person) {
					$document->addField($field.'Person_id',(string)$person->getId());
					if ($field == 'assigned' && $person->getDepartment_id()) {
						$document->addField('department_id', $person->getDepartment_id());
					}
				}
			}

			$issueFields = array('type','contactMethod');
			$description = '';
			foreach ($record->getIssues() as $issue) {
				$description.= $issue->getDescription();
				foreach ($issueFields as $field) {
					$get = 'get'.ucfirst($field);
					$document->addField($field, $issue->$get());
				}
				foreach ($issue->getLabels() as $label) {
					$document->addField('label', $label);
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
			switch ($facetFieldKey) {
				case 'client_id':
				case 'department_id':
				case 'category_id':
					$class = self::$facetFields[$recordType][$facetFieldKey];
					$o = new $class($value);
					return $o->getName();
				break;

				case 'assignedPerson_id':
					$o = new Person($value);
					return $o->getFullname();
				break;

				default:
					return $value;
			}
		}
	}

	public static function getSortableFields($recordType='ticket')
	{
		$fields = array(
			'number',
			'enteredDate',
			'status','resolution',
			'location','city','state','zip'
		);
		foreach (AddressService::$customFieldDescriptions as $key=>$value) {
			$fields[] = $key;
		}
		return $fields;
	}
}