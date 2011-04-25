<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Issue
{
	private $data = array();
	
	/**
	 * Populates the object with data
	 *
	 * Passing in an associative array of data will populate this object without
	 * hitting the database.
	 *
	 * @param array $data
	 */
	public function __construct($data)
	{
		if (is_array($data)) {
			$this->data = $data;
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->data['date'] = new MongoDate();
		}
	}
	
	/**
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * Throws an exception if anything's wrong
	 *
	 * Setting $preliminary will make the validation ignore the Ticket_id.
	 * This is usefull for validing all the user-input data before assigning
	 * the issue to a Ticket.
	 *
	 * @param bool $preliminary
	 * @throws Exception $e
	 */
	public function validate($preliminary=false)
	{
		if (!$this->data['type']) {
			throw new Exception('missingRequiredFields');
		}

		#if (!$this->data['enteredByPerson']) {
		#	throw new Exception('missingRequiredFields');
		#}

		if (!$this->data['date']) {
			$this->data['date'] = new MongoDate();
		}
	}

	//----------------------------------------------------------------
	// Generic Getters
	//----------------------------------------------------------------
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
			list($microseconds,$timestamp) = explode(' ',$this->data['date']);
			return date($format,$timestamp);
		}
		else {
			return $this->data['date'];
		}
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
	 * @return array
	 */
	public function getEnteredByPerson()
	{
		if (isset($this->data['enteredByPerson'])) {
			return $this->data['enteredByPerson'];
		}
	}

	/**
	 * @return array
	 */
	public function getReportedByPerson()
	{
		if (isset($this->data['reportedByPerson'])) {
			return $this->data['reportedByPerson'];
		}
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
	 * @return string
	 */
	public function getResponseMethod()
	{
		if (isset($this->data['responseMethod'])) {
			return $this->data['responseMethod'];
		}
	}

	/**
	 * @return text
	 */
	public function getNotes()
	{
		if (isset($this->data['notes'])) {
			return $this->data['notes'];
		}
	}

	//----------------------------------------------------------------
	// Generic Setters
	//----------------------------------------------------------------
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
	 * @param string $string
	 */
	public function setType($string)
	{
		$this->data['type'] = trim($string);
	}

	/**
	 * @param string|Person $person
	 */
	public function setEnteredByPerson($person)
	{
		if (!$person instanceof Person) {
			$person = new Person($person);
		}
		$this->data['enteredByPerson'] = array(
			'_id'=>$person->getId(),
			'fullname'=>$person-getFullname()
		);
	}

	/**
	 * @param string|Person $person
	 */
	public function setReportedByPerson($person)
	{
		if (!$person instanceof Person) {
			$person = new Person($person);
		}
		$this->data['reportedByPerson'] = array(
			'_id'=>$person->getId(),
			'fullname'=>$person-getFullname()
		);
	}

	/**
	 * @param string $string
	 */
	public function setContactMethod($string)
	{
		$this->data['contactMethod'] = trim($string);
	}
	
	/**
	 * @param string $string
	 */
	public function setResponseMethod($string)
	{
		$this->data['responseMethod'] = trim($string);
	}
	/**
	 * @param text $text
	 */
	public function setNotes($text)
	{
		$this->notes = trim($text);
	}

	//----------------------------------------------------------------
	// Custom Functions
	// We recommend adding all your custom code down here at the bottom
	//----------------------------------------------------------------
	/**
	 * @return array
	 */
	public function getHistory()
	{
		return $this->data['history'];
	}

	/**
	 * @param Media $media
	 */
	public function attachMedia(Media $media)
	{
		$this->data['media'][] = array(
			'filename'=>$media->getFilename(),
			'mime_type'=>$media->getMime_type(),
			'media_type'=>$media->getMedia_type(),
			'date'=>$media->getUploaded(),
			'person'=>array(
				'_id'=>$media->getPerson()->getId(),
				'fullname'=>$media->getPerson()->getFullname()
			);
		);
	}

	/**
	 * @return array
	 */
	public function getMedia()
	{
		$media = array();
		if ($this->id) {
			$zend_db = Database::getConnection();
			$result = $zend_db->fetchCol(
				'select media_id from issue_media where issue_id=?',
				array($this->id)
			);
			foreach ($result as $media_id) {
				$media[] = new Media($media_id);
			}
		}
		return $media;
	}
}
