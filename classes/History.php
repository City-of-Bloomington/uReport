<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class History extends MongoRecord
{
	/**
	 * @param array $data
	 */
	public function __construct($data=null)
	{
		if (isset($data)) {
			$this->data = $data;
		}
		else {
			$this->data['enteredDate'] = new MongoDate();
			$this->data['actionDate'] = new MongoDate();
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 *
	 * @throws Exception $e
	 */
	public function validate()
	{
		if (!$this->data['action']) {
			throw new Exception('missingRequiredFields');
		}

		if (!$this->data['enteredDate']) {
			$this->data['enteredDate'] = new MongoDate();
		}

		if (!$this->data['actionDate']) {
			$this->data['actionDate'] = new MongoDate();
		}
	}
	//----------------------------------------------------------------
	// Generic Getters
	//----------------------------------------------------------------
	/**
	 * @return string
	 */
	public function getAction()
	{
		if (isset($this->data['action'])) {
			return $this->data['action'];
		}
	}
	
	/**
	 * @return string
	 */
	public function getDescription()
	{
		if (isset($this->data['description'])) {
			$enteredByPerson = isset($this->data['enteredByPerson']['fullname'])
				? $this->data['enteredByPerson']['fullname']
				: '';
			$actionPerson = isset($this->data['actionPerson']['fullname'])
				? $this->data['actionPerson']['fullname']
				: '';
			return $this->parseDescription(
				array('enteredByPerson'=>$enteredByPerson,'actionPerson'=>$actionPerson)
			);
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
	 * @return string|MongoDate
	 */
	public function getEnteredDate($format=null)
	{
		if ($format) {
			return date($format,$this->data['enteredDate']->sec);
		}
		else {
			return $this->date['enteredDate'];
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
	 * @return string|MongoDate
	 */
	public function getActionDate($format=null)
	{
		if ($format) {
			return date($format,$this->data['actionDate']->sec);
		}
		else {
			return $this->date['actionDate'];
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
	public function getActionPerson()
	{
		if (isset($this->data['actionPerson'])) {
			return $this->data['actionPerson'];
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
	 * @param string $string
	 */
	public function setAction($string)
	{
		$this->data['action'] = trim($string);
	}
	
	/**
	 * Sets the date
	 *
	 * Dates should be in something strtotime() understands
	 * http://www.php.net/manual/en/function.strtotime.php
	 *
	 * @param string $date
	 */
	public function setEnteredDate($date)
	{
		$date = trim($date);
		if ($date) {
			$this->data['enteredDate'] = new MongoDate(strtotime($date));
		}
	}

	/**
	 * Sets the date
	 *
	 * Date string formats should be in something strtotime() understands
	 * http://www.php.net/manual/en/function.strtotime.php
	 *
	 * @param int|string|array $date
	 */
	public function setActionDate($date)
	{
		$date = trim($date);
		if ($date) {
			$this->data['enteredDate'] = new MongoDate(strtotime($date));
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
	 * Sets person data
	 *
	 * See: MongoRecord->setPersonData
	 *
	 * @param string|array|Person $person
	 */
	public function setActionPerson($person)
	{
		$this->setPersonData('actionPerson',$person);
	}

	/**
	 * @param text $text
	 */
	public function setNotes($text)
	{
		$this->data['notes'] = trim($text);
	}
	//----------------------------------------------------------------
	// Custom Functions
	// We recommend adding all your custom code down here at the bottom
	//----------------------------------------------------------------
	/**
	 * Substitutes actual data for the placeholders in the description
	 *
	 * Specify the placeholders as an associative array
	 * $placeholders = array('enteredByPerson'=>'Joe Smith',
	 *						'actionPerson'=>'Mary Sue')
	 *
	 * @param array $placeholders
	 * @return string
	 */
	public function parseDescription($placeholders)
	{
		$output = $this->description;

		foreach ($placeholders as $key=>$value) {
			$output = preg_replace("/\{$key\}/",$value,$output);
		}
		return $output;
	}
	
	/**
	 * Returns data from person structures in the Mongo record
	 *
	 * If the data doesn't exist an empty string will be returned
	 * Examples:
	 * 	getPersonData('enteredByPerson','id')
	 *  getPersonData('referredPerson','fullname')
	 *
	 * @param string $personField
	 * @param string $dataField
	 * @return string
	 */
	public function getPersonData($personField,$dataField)
	{
		return isset($this->data[$personField][$dataField])
			? $this->data[$personField][$dataField]
			: '';
	}
}
