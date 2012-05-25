<?php
/**
 * A class for working with Issues
 *
 * Issues are part of Ticket records
 *
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Issue extends ActiveRecord
{
	protected $tablename = 'issues';

	protected $ticket;
	protected $contactMethod;
	protected $responseMethod;
	protected $issueType;
	protected $enteredByPerson;
	protected $reportedByPerson;

	private $labels = array();
	private $labelsModified = false;

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
				$sql = 'select * from issues where id=?';
				$result = $zend_db->fetchRow($sql, array($id));
			}

			if ($result) {
				$this->data = $result;
			}
			else {
				throw new Exception('issues/unknownIssue');
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->setDate('now');

			if (isset($_SESSION['USER'])) {
				$this->setEnteredByPerson($_SESSION['USER']);
			}
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 *
	 * @param bool $preliminary
	 * @throws Exception $e
	 */
	public function validate($preliminary=false)
	{
		if (!$this->getTicket_id())    { throw new Exception('issues/missingTicket'); }
		if (!$this->getIssueType_id()) { throw new Exception('issues/missingType');   }
		if (!$this->getDate()) { $this->setDate('now'); }

		if (isset($_SESSION['USER'])) {
			if (!$this->getEnteredByPerson_id()) {
				$this->setEnteredByPerson($_SESSION['USER']);
			}
			if (!$this->getReportedByPerson_id()) {
				$this->setReportedByPerson($_SESSION['USER']);
			}
		}

	}

	public function save()
	{
		parent::save();
		if ($this->labelsModified) { $this->saveLabels(); }
	}

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()                  { return parent::get('id');                  }
	public function getTicket_id()           { return parent::get('ticket_id');           }
	public function getContactMethod_id()    { return parent::get('contactMethod_id');    }
	public function getResponseMethod_id()   { return parent::get('responseMethod_id');   }
	public function getIssueType_id()        { return parent::get('issueType_id');        }
	public function getEnteredByPerson_id()  { return parent::get('enteredByPerson_id');  }
	public function getReportedByPerson_id() { return parent::get('reportedByPerson_id'); }
	public function getDescription()         { return parent::get('description');         }
	public function getDate($format=null, DateTimeZone $timezone=null) { return parent::getDateData('date', $format, $timezone); }
	public function getContactMethod()    { return parent::getForeignKeyObject('ContactMethod', 'contactMethod_id');    }
	public function getResponseMethod()   { return parent::getForeignKeyObject('ContactMethod', 'responseMethod_id');   }
	public function getIssueType()        { return parent::getForeignKeyObject('IssueType',     'issueType_id');        }
	public function getEnteredByPerson()  { return parent::getForeignKeyObject('Person',        'enteredByPerson_id');  }
	public function getReportedByPerson() { return parent::getForeignKeyObject('Person',        'reportedByPerson_id'); }

	public function setDescription ($s) { parent::set('description', $s); }
	public function setDate($d)         { parent::setDateData('date', $d); }
	public function setTicket_id          ($id) { parent::setForeignKeyField('Ticket',        'ticket_id',           $id); }
	public function setContactMethod_id   ($id) { parent::setForeignKeyField('ContactMethod', 'contactMethod_id',    $id); }
	public function setResponseMethod_id  ($id) { parent::setForeignKeyField('ContactMethod', 'responseMethod_id',   $id); }
	public function setIssueType_id       ($id) { parent::setForeignKeyField('IssueType',     'issueType_id',        $id); }
	public function setEnteredByPerson_id ($id) { parent::setForeignKeyField('Person',        'enteredByPerson_id',  $id); }
	public function setReportedByPerson_id($id) { parent::setForeignKeyField('Person',        'reportedByPerson_id', $id); }
	public function setTicket          (Ticket        $o) { parent::setForeignKeyObject('Ticket',        'ticket_id',           $o); }
	public function setContactMethod   (ContactMethod $o) { parent::setForeignKeyObject('ContactMethod', 'contactMethod_id',    $o); }
	public function setResponseMethod  (ContactMethod $o) { parent::setForeignKeyObject('ContactMethod', 'responseMethod_id',   $o); }
	public function setIssueType       (IssueType     $o) { parent::setForeignKeyObject('IssueType',     'issueType_id',        $o); }
	public function setEnteredByPerson (Person        $o) { parent::setForeignKeyObject('Person',        'enteredByPerson_id',  $o); }
	public function setReportedByPerson(Person        $o) { parent::setForeignKeyObject('Person',        'reportedByPerson_id', $o); }

	/**
	 * @param array $post
	 */
	public function handleUpdate($post)
	{
		if (!isset($post['labels'])) {
			$post['labels'] = array();
		}
		$fields = array(
			'issueType_id', 'description', 'customFields', 'labels',
			'reportedByPerson_id', 'contactMethod_id', 'responseMethod_id'
		);
		foreach ($fields as $field) {
			$set = 'set'.ucfirst($field);
			if (isset($post[$field])) {
				$this->$set($post[$field]);
			}
		}
	}

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	/**
	 * @return array
	 */
	public function getCustomFields()
	{
		return json_decode(parent::get('customFields'));
	}

	/**
	 * @param array $array
	 */
	public function setCustomFields($array)
	{
		$this->data['customFields'] = json_encode($array);
	}

	/**
	 * Returns an array of Labels indexed by Id
	 *
	 * @return array
	 */
	public function getLabels()
	{
		if (!count($this->labels) && $this->getId()) {
			$list = new LabelList(array('issue_id'=>$this->getId()));
			foreach ($list as $label) {
				$this->labels[$label->getId()] = $label;
			}
		}
		return $this->labels;
	}

	/**
	 * Reads labels from POST array
	 *
	 * @param array $label_ids
	 */
	public function setLabels($label_ids)
	{
		$this->labelsModified = true;
		$this->labels = array();
		foreach ($label_ids as $id) {
			$label = new Label($id);
			$this->labels[$label->getId()] = $label;
		}
	}

	/**
	 * Writes the labels back out to the database
	 */
	private function saveLabels()
	{
		if ($this->getId()) {
			$zend_db = Database::getConnection();
			$zend_db->delete('issue_labels', 'issue_id='.$this->getId());
			foreach ($this->labels as $id=>$label) {
				try {
					$zend_db->insert('issue_labels', array(
						'issue_id'=>$this->data['id'],
						'label_id'=>$label->getId()
					));
				}
				catch (Exception $e) {
					echo $e->getMessage()."\n";
					print_r($this);
					exit();
				}
			}
		}
	}

	/**
	 * @param Label $l
	 * @return bool
	 */
	public function hasLabel(Label $l)
	{
		if ($this->getId()) {
			return in_array($l->getId(), array_keys($this->getLabels()));
		}
	}

	/**
	 * @return array
	 */
	public function getHistory()
	{
		$history = array();
		if (isset($this->data['history'])) {
			foreach ($this->data['history'] as $data) {
				$history[] = new History($data);
			}
		}
		return $history;
	}

	/**
	 * @return array
	 */
	public function getMedia()
	{
		$media = array();
		if (isset($this->data['media'])) {
			foreach ($this->data['media'] as $data) {
				$media[] = new Media($data);
			}
		}
		return $media;
	}

	/**
	 * @return array
	 */
	public function getResponses()
	{
		$responses = array();
		if (isset($this->data['responses'])) {
			foreach ($this->data['responses'] as $data) {
				$responses[] = new Response($data);
			}
		}
		return $responses;
	}
}
