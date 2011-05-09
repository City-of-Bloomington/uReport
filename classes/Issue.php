<?php
/**
 * A class for working with Issues
 *
 * Issues are only stored inside cases.
 * They do not have their own collection in Mongo
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Issue extends MongoRecord
{
	public static $types = array(
		'Request','Complaint','Violation'
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
			throw new Exception('missingRequiredFields');
		}

		#if (!$this->getEnteredByPerson()) {
		#	throw new Exception('missingRequiredFields');
		#}

		if (!isset($this->data['date'])) {
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
			return date($format,$this->data['date']->sec);
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
	 * @return string
	 */
	public function getProblem()
	{
		if (isset($this->data['problem'])) {
			return $this->data['problem'];
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
	 * @return array
	 */
	public function getCategory()
	{
		if (isset($this->data['category'])) {
			return $this->data['category'];
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
	 * @param string $string
	 */
	public function setProblem($string)
	{
		$this->data['problem'] = trim($string);
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
	 * @param string|Category $category
	 */
	public function setCategory($category)
	{
		if (!$category instanceof Category) {
			$category = trim($category);
			if (!$category) {
				return false;
			}
			$category = new Category($category);
		}
		$this->data['category'] = array(
			'_id'=>$category->getId(),
			'name'=>$category->getName()
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
		if (isset($this->data['history'])) {
			return $this->data['history'];
		}
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
			)
		);
	}

	/**
	 * @return array
	 */
	public function getMedia()
	{
		$media = array();
		return $media;
	}
}
