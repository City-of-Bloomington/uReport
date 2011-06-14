<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Response extends MongoRecord
{
	public function __construct($data=null)
	{
		if (isset($data)) {
			if (is_array($data)) {
				$this->data = $data;
			}
			else {
				throw new Exception('response/invalidData');
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->data['date'] = new MongoDate();
		}
	}

	public function validate()
	{
		if (!$this->data['date']) {
			$this->data['date'] = new MongoDate();
		}

		if (!$this->getPerson()) {
			throw new Exception('response/unknownPerson');
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
	public function getContactMethod()
	{
		if (isset($this->data['contactMethod'])) {
			return $this->data['contactMethod'];
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

	/**
	 * @return array
	 */
	public function getPerson()
	{
		if (isset($this->data['person'])) {
			return $this->data['person'];
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
	public function setContactMethod($string)
	{
		$this->data['contactMethod'] = trim($string);
	}

	/**
	 * @param text $text
	 */
	public function setNotes($text)
	{
		$this->data['notes'] = trim($text);
	}

	/**
	 * Sets person data
	 *
	 * See: MongoRecord->setPersonData
	 *
	 * @param string|array|Person $person
	 */
	public function setPerson($person)
	{
		$this->setPersonData('person',$person);
	}

}