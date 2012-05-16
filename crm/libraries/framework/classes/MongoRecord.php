<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
abstract class MongoRecord
{
	protected $data = array();

	/**
	 * Returns any field stored in $data
	 *
	 * @param string $fieldname
	 */
	public function get($fieldname)
	{
		if (isset($this->data[$fieldname])) {
			return $this->data[$fieldname];
		}
	}

	/**
	 * Returns raw data from the Mongo record.
	 *
	 * You can specify a particular element to return, using the dot syntax
	 * If no element is asked for, the entire data record is returned
	 *
	 * @param string $path
	 * @return array
	 */
	public function getData($path=null)
	{
		$data = $this->data;
		if ($path) {
			foreach (explode('.',$path) as $field) {
				if (isset($data[$field])) {
					$data = $data[$field];
				}
				else {
					return null;
				}
			}
		}
		return $data;
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

	/**
	 * Loads the Person from Mongo and returns the Person object
	 *
	 * @param string $personField
	 * @return Person
	 */
	public function getPersonObject($personField)
	{
		if (isset($this->data[$personField]['_id'])) {
			return new Person((string)$this->data[$personField]['_id']);
		}
	}

	/**
	 * @param string $fieldname
	 * @param string|array|Person $person
	 */
	public function setPersonData($fieldname, $person)
	{
		if (is_string($person)) {
			$person = new Person($person);
		}
		if (is_array($person)) {
			if (isset($person['_id'])) {
				$person = new Person("$person[_id]");
			}
		}
		if ($person instanceof Person) {
			$this->data[$fieldname] = $person->getData();
		}
		else {
			throw new Exception('invalidPerson');
		}
	}

	/**
	 * Returns the date/time in the desired format
	 *
	 * Format is specified using PHP's date() syntax
	 * http://www.php.net/manual/en/function.date.php
	 * If no format is given, the MongoDate object is returned
	 *
	 * @param string $field
	 * @param string $format
	 * @param DateTimeZone $timezone
	 * @return string|MongoDate
	 */
	public function getDateData($dateField, $format=null, DateTimeZone $timezone=null)
	{
		if (isset($this->data[$dateField])) {
			if ($format) {
				$date = DateTime::createFromFormat('U', $this->data[$dateField]->sec);
				if ($timezone) { $date->setTimezone($timezone); }
				return $date->format($format);
			}
			else {
				return $this->data[$dateField];
			}
		}
	}

	/**
	 * Sets a date
	 *
	 * Dates should be in something strtotime() understands
	 * http://www.php.net/manual/en/function.strtotime.php
	 *
	 * @param string $dateField
	 * @param string $date
	 */
	public function setDateData($dateField, $date)
	{
		$date = trim($date);
		if ($date) {
			$this->data[$dateField] = new MongoDate(strtotime($date));
		}
	}
}
