<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
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
			if (isset($_SESSION['USER'])) {
				$this->setEnteredByPerson($_SESSION['USER']);
				$this->setActionPerson($_SESSION['USER']);
			}
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 *
	 * @throws Exception $e
	 */
	public function validate()
	{
		if (!$this->getAction()) {
			throw new Exception('history/missingAction');
		}

		if (!$this->data['enteredDate']) {
			$this->data['enteredDate'] = new MongoDate();
		}

		if (!$this->data['actionDate']) {
			$this->data['actionDate'] = new MongoDate();
		}

		if (isset($_SESSION['USER'])) {
			if (!isset($this->data['enteredByPerson'])) {
				$this->setEnteredByPerson($_SESSION['USER']);
			}
			if (!isset($this->data['actionPerson'])) {
				$this->setActionPerson($_SESSION['USER']);
			}
		}
	}

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
	 * @param string $string
	 */
	public function setAction($string)
	{
		$this->data['action'] = trim($string);
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
			$this->data['actionDate'] = new MongoDate(strtotime($date));
		}
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
	public function getActionPerson()
	{
		if (isset($this->data['actionPerson'])) {
			return new Person($this->data['actionPerson']);
		}
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
	 * @return text
	 */
	public function getNotes()
	{
		if (isset($this->data['notes'])) {
			return $this->data['notes'];
		}
	}

	/**
	 * @param text $text
	 */
	public function setNotes($text)
	{
		$this->data['notes'] = trim($text);
	}

	/**
	 * Returns the parsed description
	 *
	 * This is where the placeholders are defined
	 * Add any placeholders and their values to the array being
	 * passed to $this->parseDescription()
	 *
	 * @return string
	 */
	public function getDescription()
	{
		foreach (ActionList::getActions() as $action) {
			if ($action->getName()==$this->getAction()) {
				$enteredByPerson = $this->getEnteredByPerson();
				$enteredByPerson = $enteredByPerson ? $enteredByPerson->getFullname() : '';

				$actionPerson = $this->getActionPerson();
				$actionPerson = $actionPerson ? $actionPerson->getFullname() : '';

				return $this->parseDescription(
					$action->getDescription(),
					array(
						'enteredByPerson'=>$enteredByPerson,
						'actionPerson'=>$actionPerson
					)
				);
			}
		}
	}

	/**
	 * Substitutes actual data for the placeholders in the description
	 *
	 * Specify the placeholders as an associative array
	 * $placeholders = array('enteredByPerson'=>'Joe Smith',
	 *						'actionPerson'=>'Mary Sue')
	 *
	 * @param string $description
	 * @param array $placeholders
	 * @return string
	 */
	public function parseDescription($description,$placeholders)
	{
		foreach ($placeholders as $key=>$value) {
			$description = preg_replace("/\{$key\}/",$value,$description);
		}
		return $description;
	}

	/**
	 * Send a notification of this action to the actionPerson
	 *
	 * Does not send if the enteredByPerson and actionPerson are the same person
	 * @param Ticket $ticket
	 */
	public function sendNotification($ticket=null)
	{
		$enteredByPerson = $this->getPersonObject('enteredByPerson');
		$actionPerson = $this->getPersonObject('actionPerson');
		$url = $ticket ? $ticket->getURL() : '';

		if ($enteredByPerson && $actionPerson
			&& "{$enteredByPerson->getId()}" != "{$actionPerson->getId()}") {

			$actionPerson->sendNotification(
				"$url\n\n{$this->getDescription()}\n\n{$this->getNotes()}",
				APPLICATION_NAME.' '.$this->getAction(),
				$enteredByPerson
			);
		}
	}
}
