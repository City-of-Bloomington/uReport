<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
abstract class History
{
	protected $id;
	protected $action_id;
	protected $enteredDate;
	protected $enteredByPerson_id;
	protected $actionDate;
	protected $actionPerson_id;
	protected $notes;

	protected $action;
	protected $enteredByPerson;
	protected $actionPerson;

	//----------------------------------------------------------------
	// Generic Getters
	//----------------------------------------------------------------
	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return int
	 */
	public function getAction_id()
	{
		return $this->action_id;
	}

	/**
	 * @return Action
	 */
	public function getAction()
	{
		if ($this->action_id) {
			if (!$this->action) {
				$this->action = new Action($this->action_id);
			}
			return $this->action;
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
	public function getEnteredDate($format=null)
	{
		if ($format && $this->enteredDate) {
			return $this->enteredDate->format($format);
		}
		else {
			return $this->enteredDate;
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
	public function getActionDate($format=null)
	{
		if ($format && $this->actionDate) {
			return $this->actionDate->format($format);
		}
		else {
			return $this->actionDate;
		}
	}

	/**
	 * @return int
	 */
	public function getEnteredByPerson_id()
	{
		return $this->enteredByPerson_id;
	}

	/**
	 * @return Person
	 */
	public function getEnteredByPerson()
	{
		if ($this->enteredByPerson_id) {
			if (!$this->enteredByPerson) {
				$this->enteredByPerson = new Person($this->enteredByPerson_id);
			}
			return $this->enteredByPerson;
		}
		return null;
	}

	/**
	 * @return int
	 */
	public function getActionPerson_id()
	{
		return $this->actionPerson_id;
	}

	/**
	 * @return Person
	 */
	public function getActionPerson()
	{
		if ($this->actionPerson_id) {
			if (!$this->actionPerson) {
				$this->actionPerson = new Person($this->actionPerson_id);
			}
			return $this->actionPerson;
		}
		return null;
	}

	/**
	 * @return text
	 */
	public function getNotes()
	{
		return $this->notes;
	}

	//----------------------------------------------------------------
	// Generic Setters
	//----------------------------------------------------------------
	/**
	 * @param int $int
	 */
	public function setAction_id($int)
	{
		$this->action = new Action($int);
		$this->action_id = $this->action->getId();
	}

	/**
	 * @param string|Action $action
	 */
	public function setAction($action)
	{
		if (!$action instanceof Action) {
			$action = new Action($action);
		}
		$this->action_id = $action->getId();
		$this->action = $action;
	}

	/**
	 * Sets the date
	 *
	 * Date arrays should match arrays produced by getdate()
	 *
	 * Date string formats should be in something strtotime() understands
	 * http://www.php.net/manual/en/function.strtotime.php
	 *
	 * @param int|string|array $date
	 */
	public function setEnteredDate($date)
	{
		if ($date instanceof Date) {
			$this->enteredDate = $date;
		}
		elseif ($date) {
			$this->enteredDate = new Date($date);
		}
		else {
			$this->enteredDate = null;
		}
	}

	/**
	 * Sets the date
	 *
	 * Date arrays should match arrays produced by getdate()
	 *
	 * Date string formats should be in something strtotime() understands
	 * http://www.php.net/manual/en/function.strtotime.php
	 *
	 * @param int|string|array $date
	 */
	public function setActionDate($date)
	{
		if ($date instanceof Date) {
			$this->actionDate = $date;
		}
		elseif ($date) {
			$this->actionDate = new Date($date);
		}
		else {
			$this->actionDate = null;
		}
	}

	/**
	 * @param int $int
	 */
	public function setEnteredByPerson_id($int)
	{
		$this->enteredByPerson = new Person($int);
		$this->enteredByPerson_id = $int;
	}

	/**
	 * @param Person $person
	 */
	public function setEnteredByPerson($person)
	{
		$this->enteredByPerson_id = $person->getId();
		$this->enteredByPerson = $person;
	}

	/**
	 * @param int $int
	 */
	public function setActionPerson_id($int)
	{
		$this->actionPerson = new Person($int);
		$this->actionPerson_id = $int;
	}

	/**
	 * @param Person $person
	 */
	public function setActionPerson($person)
	{
		$this->actionPerson_id = $person->getId();
		$this->actionPerson = $person;
	}

	/**
	 * @param text $text
	 */
	public function setNotes($text)
	{
		$this->notes = $text;
	}
	//----------------------------------------------------------------
	// Custom Functions
	// We recommend adding all your custom code down here at the bottom
	//----------------------------------------------------------------
	/**
	 * @return string
	 */
	public function getDescription()
	{
		$enteredByPerson = $this->getEnteredByPerson() ? $this->getEnteredByPerson()->getFullname() : '';
		$actionPerson = $this->getActionPerson() ? $this->getActionPerson()->getFullname() : '';

		return $this->getAction()->parseDescription(
			array('enteredByPerson'=>$enteredByPerson,'actionPerson'=>$actionPerson)
		);
	}
}