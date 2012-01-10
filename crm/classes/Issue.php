<?php
/**
 * A class for working with Issues
 *
 * Issues are only stored inside Tickets.
 * They do not have their own collection in Mongo
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Issue extends MongoRecord
{
	public static $types = array(
		'Request','Complaint','Violation','Police Report',
		'Public Report','Comment','Question','Staff Report'
	);

	public static $contactMethods = array(
		'Phone','Email','Letter','Mayor Email','Constituent Meeting','Walk In','Web Form'
	);

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

	/**
	 * Returns the date/time in the desired format
	 *
	 * Format is specified using PHP's date() syntax
	 * http://www.php.net/manual/en/function.date.php
	 * If no format is given, the Date object is returned
	 *
	 * @param string $format
	 * @return string|DateTime
	 */
	public function getDate($format=null)
	{
		if ($format) {
			return date($format,$this->data['date']->sec);
		}
		else {
			return $this->data['date'];
		}
	}

	/**
	 * Sets the date
	 *
	 * Date string formats should be in something strtotime() understands
	 * http://www.php.net/manual/en/function.strtotime.php
	 *
	 * @param string|MongoDate $date
	 */
	public function setDate($date)
	{
		if (!$date instanceof MongoDate) {
			$date = trim($date);
			$date = new MongoDate(strtotime($date));
		}
		$this->data['date'] = $date;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		if (isset($this->data['type'])) {
			return $this->data['type'];
		}
	}

	/**
	 * @param string $string
	 */
	public function setType($string)
	{
		$this->data['type'] = trim($string);
	}

	/**
	 * @return Person
	 */
	public function getEnteredByPerson()
	{
		if (isset($this->data['enteredByPerson'])) {
			return new Person($this->data['enteredByPerson']);
		}
	}

	/**
	 * Sets person data
	 *
	 * See: MongoRecord->setPersonData
	 *
	 * @param string|array|Person $person
	 */
	public function setEnteredByPerson($person)
	{
		$this->setPersonData('enteredByPerson',$person);
	}

	/**
	 * @return Person
	 */
	public function getReportedByPerson()
	{
		if (isset($this->data['reportedByPerson'])) {
			return new Person($this->data['reportedByPerson']);
		}
	}

	/**
	 * Sets person data
	 *
	 * See: MongoRecord->setPersonData
	 *
	 * @param string|array|Person $person
	 */
	public function setReportedByPerson($person)
	{
		$this->setPersonData('reportedByPerson',$person);
	}

	/**
	 * @return string
	 */
	public function getContactMethod()
	{
		if (isset($this->data['contactMethod'])) {
			return $this->data['contactMethod'];
		}
	}

	/**
	 * @param string $string
	 */
	public function setContactMethod($string)
	{
		$this->data['contactMethod'] = trim($string);
	}

	/**
	 * @return string
	 */
	public function getResponseMethod()
	{
		if (isset($this->data['responseMethod'])) {
			return $this->data['responseMethod'];
		}
	}

	/**
	 * @param string $string
	 */
	public function setResponseMethod($string)
	{
		$this->data['responseMethod'] = trim($string);
	}

	/**
	 * @return text
	 */
	public function getDescription()
	{
		if (isset($this->data['description'])) {
			return $this->data['description'];
		}
	}

	/**
	 * @param text $text
	 */
	public function setDescription($text)
	{
		$this->data['description'] = trim($text);
	}

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
	 * @return array An array of Response objects
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

	/**
	 * @return array
	 */
	public function getCustomFields()
	{
		if (isset($this->data['customFields'])) {
			return $this->data['customFields'];
		}
	}

	/**
	 * @param array $post
	 */
	public function setCustomFields($post)
	{
		$this->data['customFields'] = $post;
	}

	/**
	 * Populates available fields from the given array
	 *
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
}
