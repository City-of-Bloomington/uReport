<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Ticket extends ActiveRecord
{
	protected $tablename = 'tickets';

	protected $substatus;
	protected $category;
	protected $client;
	protected $enteredByPerson;
	protected $assignedPerson;
	protected $referredPerson;

	private $issues;
	private $needToUpdateClusters = false;

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
				$sql = 'select * from tickets where id=?';
				$result = $zend_db->fetchRow($sql, array($id));
			}

			if ($result) {
				$this->data = $result;
			}
			else {
				throw new Exception('tickets/unknownTicket');
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->setEnteredDate('now');
			$this->setStatus('open');
			$this->setCity(DEFAULT_CITY);
			$this->setState(DEFAULT_STATE);
			if (isset($_SESSION['USER'])) {
				$this->setEnteredByPerson($_SESSION['USER']);
			}
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		// Check for required fields here.  Throw an exception if anything is missing.
		if (!$this->getCategory()) {
			throw new Exception('tickets/missingCategory');
		}

		$issue = $this->getIssue();
		if (!$issue) {
			throw new Exception('tickets/missingIssue');
		}

		// We need at least a location (address or lat/long) or a description
		// an empty ticket does us no good
		$lat  = $this->getLatitude();
		$long = $this->getLongitude();
		if (!$issue->getDescription() && !$this->getLocation() && !($lat && $long)) {
			throw new Exception('missingRequiredFields');
		}
		if (($this->getLatitude() && $this->getLongitude())
			&& (   defined('MIN_LATITUDE')  && defined('MAX_LATITUDE')
				&& defined('MIN_LONGITUDE') && defined('MAX_LONGITUDE'))) {
			if (!(   MIN_LATITUDE <=$lat  && $lat <=MAX_LATITUDE
				  && MIN_LONGITUDE<=$long && $long<=MAX_LONGITUDE)) {
				throw new Exception('tickets/locationOutOfBounds');
			}
		}

		// The rest of these fields can be populated, if they're not provided
		if (!$this->getStatus()) { $this->setStatus('open'); }
		if ($this->getSubstatus_id()) {
			if ($this->getSubstatus()->getStatus() != $this->getStatus()) {
				throw new Exception('tickets/statusMismatch');
			}
		}
		else {
			if ($this->getStatus()=='closed') {
				throw new Exception('tickets/missingResolution');
			}
		}

		if (!$this->data['enteredDate']) {
			$this->setEnteredDate('now');
		}

		// Don't auto-populate the enteredByPerson except during ticket creation
		if (!$this->getId() && !$this->getEnteredByPerson_id()) {
			if (isset($_SESSION['USER'])) {
				$this->setEnteredByPerson($_SESSION['USER']);
			}
		}

		if (!$this->getAssignedPerson_id()) {
			$c = $this->getCategory();
			if ($c->getDepartment_id()) {
				$d = $c->getDepartment();
				$this->setAssignedPerson($d->getDefaultPerson());
			}
			else {
				$this->setAssignedPerson($_SESSION['USER']);
			}
		}

	}

	public function updateSearchIndex()
	{
		$search = new Search();
		$search->add($this);
		$search->solrClient->commit();
	}

	public function save()
	{
		$this->setLastModified(date(DATE_FORMAT));
		if ($this->needToUpdateClusters) {
			for ($i=0; $i<=6; $i++) {
				$this->setClusterId($i, null);
			}
			$this->assignClusterIds();
		}
		parent::save();
		$this->updateSearchIndex();
	}

	public function delete()
	{
		foreach ($this->getIssues()  as $i) { $i->delete(); }
		foreach ($this->getHistory() as $h) { $h->delete(); }

		$search = new Search();
		$search->delete($this);
		$search->solrClient->commit();

		parent::delete();
	}

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()           { return parent::get('id');         }
	public function getAddressId()    { return parent::get('addressId');  }
	public function getLocation()     { return parent::get('location');   }
	public function getCity()         { return parent::get('city');       }
	public function getState()        { return parent::get('state');      }
	public function getZip()          { return parent::get('zip');        }
	public function getStatus()       { return parent::get('status');     }
	public function getEnteredDate ($f=null, DateTimeZone $tz=null) { return parent::getDateData('enteredDate',  $f, $tz); }
	public function getLastModified($f=null, DateTimeZone $tz=null) { return parent::getDateData('lastModified', $f, $tz); }
	public function getClosedDate  ($f=null, DateTimeZone $tz=null) { return parent::getDateData('closedDate',   $f, $tz); }
	public function getSubstatus_id()       { return parent::get('substatus_id');       }
	public function getCategory_id()        { return parent::get('category_id');        }
	public function getClient_id()          { return parent::get('client_id');          }
	public function getEnteredByPerson_id() { return parent::get('enteredByPerson_id'); }
	public function getAssignedPerson_id()  { return parent::get('assignedPerson_id');  }
	public function getReferredPerson_id()  { return parent::get('referredPerson_id');  }
	public function getSubstatus()       { return parent::getForeignKeyObject('Substatus',  'substatus_id');       }
	public function getCategory()        { return parent::getForeignKeyObject('Category',   'category_id');        }
	public function getClient()          { return parent::getForeignKeyObject('Client',     'client_id');          }
	public function getEnteredByPerson() { return parent::getForeignKeyObject('Person',     'enteredByPerson_id'); }
	public function getAssignedPerson()  { return parent::getForeignKeyObject('Person',     'assignedPerson_id');  }
	public function getReferredPerson()  { return parent::getForeignKeyObject('Person',     'referredPerson_id');  }

	public function getLatitude()     { return parent::get('latitude');   }
	public function getLongitude()    { return parent::get('longitude');  }
	public function getClusterId($lv) { return parent::get('cluster_id_lv'.$lv); }


	public function setAddressId($s)  { parent::set('addressId', $s); }
	public function setLocation ($s)  { parent::set('location',  $s); }
	public function setCity     ($s)  { parent::set('city',      $s); }
	public function setState    ($s)  { parent::set('state',     $s); }
	public function setZip      ($s)  { parent::set('zip',       $s); }
	public function setEnteredDate ($date) { parent::setDateData('enteredDate',  $date); }
	public function setLastModified($date) { parent::setDateData('lastModified', $date); }
	public function setClosedDate  ($date) { parent::setDateData('closedDate',   $date); }
	public function setSubstatus_id      ($id) { parent::setForeignKeyField('Substatus',  'substatus_id',       $id); }
	public function setCategory_id       ($id) { parent::setForeignKeyField('Category',   'category_id',        $id); }
	public function setClient_id         ($id) { parent::setForeignKeyField('Client',     'client_id',          $id); }
	public function setEnteredByPerson_id($id) { parent::setForeignKeyField('Person',     'enteredByPerson_id', $id); }
	public function setAssignedPerson_id ($id) { parent::setForeignKeyField('Person',     'assignedPerson_id',  $id); }
	public function setReferredPerson_id ($id) { parent::setForeignKeyField('Person',     'referredPerson_id',  $id); }
	public function setSubstatus      (Substatus  $o) { parent::setForeignKeyObject('Substatus','substatus_id',       $o); }
	public function setCategory       (Category   $o) { parent::setForeignKeyObject('Category', 'category_id',        $o); }
	public function setClient         (Client     $o) { parent::setForeignKeyObject('Client',   'client_id',          $o); }
	public function setEnteredByPerson(Person     $o) { parent::setForeignKeyObject('Person',   'enteredByPerson_id', $o); }
	public function setAssignedPerson (Person     $o) { parent::setForeignKeyObject('Person',   'assignedPerson_id',  $o); }
	public function setReferredPerson (Person     $o) { parent::setForeignKeyObject('Person',   'referredPerson_id',  $o); }

	public function setLatitude ($s)  {
		if (!empty($s) && $this->getLatitude() != (float)$s) {
			$needToUpdateClusters = true;
		}
		parent::set('latitude',  $s);
	}
	public function setLongitude($s)  {
		if (!empty($s) && $this->getLongitude() != (float)$s) {
			$needToUpdateClusters = true;
		}
		parent::set('longitude', $s);
	}
	/**
	 * @param int $lvl
	 * @param string $cid
	 */
	public function setClusterId($lv, $cid) { parent::set('cluster_id_lv'.$lv, $cid); }
	/**
	 * @param bool $b
	 */
	public function setRecalculateClusters($b) { $this->needToUpdateClusters = $b ? true: false; }


	public function getReadyClusterId($level) {
		$sql = "SELECT MAX(cluster_id_lv$level) AS max FROM {$this->tablename}";
		$zend_db = Database::getConnection();
		$query = $zend_db->query($sql);
		$row = $query->fetch();
		return ($row['max'] === NULL) ? 1 : $row['max'] + 1;
	}

	public function assignClusterIdLv($level) {
		$id   = $this->getId();
		$lat  = $this->getLatitude();
		$lng  = $this->getLongitude();
		$dist = 0.01 * pow(2, $level * 2);
		$sql  = "
		SELECT
			t.cluster_id_lv$level,
			(SELECT round(AVG(latitude),  14) FROM tickets i where i.cluster_id_lv$level = t.cluster_id_lv$level) as mean_lat,
			(SELECT round(AVG(longitude), 14) FROM tickets i where i.cluster_id_lv$level = t.cluster_id_lv$level) as mean_lng,
			(
				SELECT
				(ACOS(SIN(RADIANS(mean_lat)) * SIN(RADIANS($lat))
				 + COS(RADIANS(mean_lat))    * COS(RADIANS($lat))
				 * COS(RADIANS(mean_lng - $lng))) * 6371.0)
			) as distance
		FROM tickets t
		WHERE
			t.cluster_id_lv$level IS NOT NULL AND
			(ACOS(SIN(RADIANS(latitude)) * SIN(RADIANS($lat))
			 + COS(RADIANS(latitude))    * COS(RADIANS($lat))
			 * COS(RADIANS(longitude - $lng))) * 6371.0) < $dist
		GROUP BY t.cluster_id_lv$level
		order by distance
		LIMIT 1
		";

		$zend_db = Database::getConnection();
		$query = $zend_db->query($sql);
		$row = $query->fetch();
		if ($row) {
			$closestClusterId = $row['cluster_id_lv'.$level];
			$this->setClusterId($level, $closestClusterId);
		}
		else {
			$readyClusterId = $this->getReadyClusterId($level);
			$this->setClusterId($level, $readyClusterId);
		}
	}

	/**
	 *
	 */
	public function assignClusterIds() {
		for($level = 0; $level <= 6; $level ++) {
			if(!$this->getClusterId($level)) {
				$this->assignClusterIdLv($level);
			}
		}
	}

	/**
	 * Update the status and substatus
	 *
	 * The new status will delete the current substatus if
	 * the current substatus is not valid for the new status
	 *
	 * @param string $string
	 * @param int $substatus_id
	 */
	public function setStatus($status, $substatus_id=null)
	{
		$oldStatus      = $this->getStatus();
		$oldSubStatusId = $this->getSubstatus_id();

		parent::set('status', $status);

		if ($substatus_id) {
			try {
				$substatus = new Substatus($substatus_id);
				if ($substatus->getStatus() == $this->getStatus()) {
					$this->setSubstatus($substatus);
				}
			}
			catch (Exception $e) {
				// Invalid substatus will just ignored
			}
		}

		if ($this->getSubstatus_id()) {
			if ($this->getSubstatus()->getStatus() != $this->getStatus()) {
				$this->setSubstatus_id(null);
			}
		}

		// See if we need to update the closedDate
		$newStatus = $this->getStatus();
		if ($newStatus == 'closed') {
			if ($newStatus != $oldStatus || $this->getSubstatus_id() != $oldSubStatusId) {
				$this->setClosedDate(date(DATE_FORMAT));
			}
		}
	}

	/**
	 * @return array
	 */
	public function getAdditionalFields()
	{
		$s = parent::get('additionalFields');
		if (!$s) { $s = '{}'; }
		return json_decode($s);
	}
	/**
	 * @param array $array
	 */
	public function setAdditionalFields($array)
	{
		$this->data['additionalFields'] = json_encode($array);
	}

	//----------------------------------------------------------------
	// Custom functions
	//----------------------------------------------------------------

	/**
	 * Returns the department of the person this ticket is assigned to.
	 *
	 * @return Department
	 */
	public function getDepartment()
	{
		$person = $this->getAssignedPerson();
		if ($person && $person->getDepartment_id()) {
			return $person->getDepartment();
		}
	}



	/**
	 * @return string
	 */
	public function getLatLong()
	{
		if ($this->getLatitude() && $this->getLongitude()) {
			return "{$this->getLatitude()},{$this->getLongitude()}";
		}
	}

	/**
	 * @return IssueList
	 */
	public function getIssues()
	{
		if (!$this->issues) {
			$this->issues = array();

			$zend_db = Database::getConnection();
			$sql = 'select * from issues where ticket_id=?';
			$result = $zend_db->query($sql, array($this->getId()));
			foreach ($result as $row) {
				$this->issues[] = new Issue($row);
			}
		}
		return $this->issues;
	}

	/**
	 * Returns a single issue
	 *
	 * Defaults to the first issue, if you don't provide an index
	 *
	 * @param int $index
	 * @param Issue
	 */
	public function getIssue($index=0)
	{
		$list = $this->getIssues();
		if (isset($list[$index])) {
			return $list[$index];
		}
	}

	/**
	 * Returns the description of the first issue in this ticket
	 *
	 * @return string
	 */
	public function getDescription()
	{
		$issue = $this->getIssue();
		return $issue ? $issue->getDescription() : '';
	}

	/**
	 * Records that someone responded to an issue
	 *
	 * @param int $index The issue index
	 * @param Response $response
	 */
	public function addResponse($index, Response $response)
	{
		$response->validate();

		$this->data['issues'][$index]['responses'][] = $response->getData();
	}

	/**
	 * @return array
	 */
	public function getHistory()
	{
		$history = array();

		$zend_db = Database::getConnection();
		$sql = 'select * from ticketHistory where ticket_id=?';
		$result = $zend_db->query($sql, array($this->getId()));
		foreach ($result as $row) {
			$history[] = new TicketHistory($row);
		}
		return $history;
	}


	/**
	 * @return string
	 */
	public function getURL()
	{
		return BASE_URL."/tickets/view?ticket_id={$this->getId()}";
	}

	/**
	 * Transfers issues and history from another ticket into this one
	 *
	 * We're only migrating the issue and history
	 * Once we're done we delete the other ticket
	 *
	 * @param Ticket $ticket
	 */
	public function mergeFrom(Ticket $ticket)
	{
		if ($this->getId()) {
			$zend_db = Database::getConnection();
			$zend_db->beginTransaction();
			try {
				$zend_db->update('ticketHistory', array('ticket_id'=>$this->getId()), 'ticket_id='.$ticket->getId());
				$zend_db->update('issues',        array('ticket_id'=>$this->getId()), 'ticket_id='.$ticket->getId());
				$zend_db->delete('tickets', 'id='.$ticket->getId());
			}
			catch (Exception $e) {
				$zend_db->rollBack();
				throw $e;
			}
			$zend_db->commit();

			$search = new Search();
			$search->delete($ticket);
			$search->add($this);
			$search->solrClient->commit();
		}
	}

	/**
	 * @param array $data
	 */
	public function setAddressServiceData($data)
	{
		foreach ($data as $key=>$value) {
			$set = 'set'.ucfirst($key);
			if (method_exists($this,$set)) {
				$this->$set($value);
			}
			else {
				$d = $this->getAdditionalFields();
				$d->$key = (string)$value;
				$this->setAdditionalFields($d);
			}
		}
	}

	/**
	 * Empties out the fields that can be populated from the AddressService
	 *
	 * New AddressService data may not include all the possible fields
	 * that were set from a previous attempt.  This function will clear
	 * out all possible fields.
	 */
	public function clearAddressServiceData()
	{
		// Used to identify fields that can be updated from the AddressService
		$addressServiceFields = array(
			'location','addressId','city','state','zip','latitude','longitude'
		);
		foreach ($addressServiceFields as $field) {
			$set = 'set'.ucfirst($field);
			$this->$set('');
		}
		foreach (AddressService::$customFieldDescriptions as $key=>$definition) {
			$d = $this->getAdditionalFields();
			if (isset($d->$key)) { unset($d->$key); }
			$this->setAdditionalFields($d);
		}
	}

	/**
	 * Populates available fields from the given array
	 *
	 * @param array $post
	 */
	public function handleUpdate($post)
	{
		// Set all the location information using any fields the user posted
		$fields = array(
			'category_id', 'client_id', 'assignedPerson_id',
			'location', 'latitude', 'longitude', 'city', 'state', 'zip'
		);
		foreach ($fields as $field) {
			if (isset($post[$field])) {
				$set = 'set'.ucfirst($field);
				$this->$set($post[$field]);
			}
		}

		// If they gave us an address, and we don't have any additional info,
		// try and get the data from Master Address
		if ($this->getLocation()
			&& (!$this->getLatitude() || !$this->getLongitude()
				|| !$this->getCity() || !$this->getState() || !$this->getZip())) {
			$data = AddressService::getLocationData($this->getLocation());
			if ($data) {
				$this->setAddressServiceData($data);
			}
		}
	}

	/**
	 * Does all the database work for TicketController::add
	 *
	 * Saves the ticket, the issue, and creates history entries
	 * for the open and assignment actions.
	 *
	 * This function calls save() as needed.  After using this function,
	 * there's no need to make an additional save() call.
	 *
	 * @param array $post
	 */
	public function handleAdd($post)
	{
		$zend_db = Database::getConnection();
		$zend_db->beginTransaction();
		try {
			$this ->handleUpdate($post);

			// We must add an issue to the ticket for validation to pass
			$issue = new Issue();
			$issue->handleUpdate($post);
			$this->issues = array($issue);

			if (!$this->getEnteredByPerson_id() && $issue->getReportedByPerson_id()) {
				$this->setEnteredByPerson_id($issue->getReportedByPerson_id());
			}

			$this->save();

			$issue->setTicket($this);
			$issue->save();

			$history = new TicketHistory();
			$history->setTicket($this);
			$history->setAction(new Action('open'));
			if ($this->getEnteredByPerson_id()) {
				$history->setEnteredByPerson_id($this->getEnteredByPerson_id());
			}
			$history->save();

			$history = new TicketHistory();
			$history->setTicket($this);
			$history->setAction(new Action('assignment'));
			$history->setActionPerson_id($this->getAssignedPerson_id());
			if (!empty($post['notes'])) {
				$history->setNotes($post['notes']);
			}
			if ($this->getEnteredByPerson_id()) {
				$history->setEnteredByPerson_id($this->getEnteredByPerson_id());
			}
			$history->save();
		}
		catch (Exception $e) {
			$zend_db->rollBack();

			$search = new Search();
			$search->delete($this);
			$search->solrClient->commit();

			throw $e;
		}
		$zend_db->commit();
		$history->sendNotification($this);
	}

	/**
	 * Checks whether the user is supposed to be allowed to see this ticket
	 *
	 * @param Person $person
	 * @return bool
	 */
	public function allowsDisplay($person)
	{
		$category = $this->getCategory_id() ? $this->getCategory() : new Category();
		return $category->allowsDisplay($person);
	}

	/**
	 * @return int
	 */
	public function getSlaDays()
	{
		$category = $this->getCategory();
		if ($category) {
			return $category->getSlaDays();
		}
	}

	/**
	 * @return int
	 */
	public function getSlaPercentage()
	{
		$days = $this->getSlaDays();
		if ($days) {
			$dateEntered = new DateTime($this->getEnteredDate());
			$targetDate = $this->getStatus()=='open'
				? new DateTime()
				: new DateTime($this->getClosedDate());
			$diff = $targetDate->diff($dateEntered);
			$daysPassed = $diff->format('%a');
			return round($daysPassed/$days*100);
		}
	}

	/**
	 * @return PeopleList
	 */
	public function getReportedByPeople()
	{
		if ($this->getId()) {
			return new PersonList(array('reportedTicket_id'=>$this->getId()));
		}
	}
}
