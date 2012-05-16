<?php
/**
 * A class for working with Issues
 *
 * Issues are only stored inside Tickets.
 * They do not have their own collection in Mongo
 *
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Issue extends MongoRecord
{
	/**
	 * Populates the object with data
	 *
	 * @param array $data
	 */
	public function __construct($data=null)
	{
		if (isset($data)) {
			if (is_array($data)) {
				$this->data = $data;
			}
			else {
				throw new Exception('issue/invalidData');
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->data['date'] = new MongoDate();

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
		if (!$this->getType()) {
			$this->setType('Request');
		}

		if (isset($_SESSION['USER'])) {
			if (!isset($this->data['enteredByPerson'])) {
				$this->setEnteredByPerson($_SESSION['USER']);
			}
			if (!isset($this->data['reportedByPerson'])) {
				$this->setReportedByPerson($_SESSION['USER']);
			}
		}

		if (!isset($this->data['date'])) {
			$this->data['date'] = new MongoDate();
		}
	}

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getType()           { return parent::get('type'); }
	public function getContactMethod()  { return parent::get('contactMethod'); }
	public function getResponseMethod() { return parent::get('responseMethod'); }
	public function getDescription()    { return parent::get('description'); }
	public function getCustomFields()   { return parent::get('customFields'); }
	public function getDate($format=null, DateTimeZone $timezone=null) { return parent::getDateData('date', $format, $timezone); }
	public function getEnteredByPerson()  { return parent::getPersonObject('enteredByPerson'); }
	public function getReportedByPerson() { return parent::getPersonObject('reportedByPerson'); }

	public function setType          ($s) { $this->data['type']           = trim($s); }
	public function setContactMethod ($s) { $this->data['contactMethod']  = trim($s); }
	public function setResponseMethod($s) { $this->data['responseMethod'] = trim($s); }
	public function setDescription   ($s) { $this->data['description']    = trim($s); }
	public function setCustomFields($array) { $this->data['customFields'] = $array; }
	public function setDate($date) { parent::setDateData('date', $date); }
	public function setEnteredByPerson ($person) { parent::setPersonData('enteredByPerson', $person); }
	public function setReportedByPerson($person) { parent::setPersonData('reportedByPerson',$person); }

	/**
	 * @param array $post
	 */
	public function set($post)
	{
		if (!isset($post['labels'])) {
			$post['labels'] = array();
		}
		$fields = array(
			'type','reportedByPerson','contactMethod','responseMethod','description',
			'customFields','labels'
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
	 * @return string
	 */
	public function getLabels()
	{
		if (isset($this->data['labels'])) {
			return $this->data['labels'];
		}
		return array();
	}

	/**
	 * @param array $labels
	 */
	public function setLabels($labels)
	{
		array_walk($labels, function($value,$key) use(&$labels) {
			$labels[$key] = trim($value);
		});
		$this->data['labels'] = $labels;
	}

	/**
	 * @param string $label
	 * @return bool
	 */
	public function hasLabel($label)
	{
		return in_array($label,$this->getLabels());
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
