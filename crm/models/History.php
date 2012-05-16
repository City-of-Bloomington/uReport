<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
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

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getAction() { return parent::get('action'); }
	public function getNotes()  { return parent::get('notes');  }
	public function getEnteredDate($format=null, DateTimeZone $timezone=null) { return parent::getDateData('enteredDate', $format, $timezone); }
	public function getActionDate ($format=null, DateTimeZone $timezone=null) { return parent::getDateData('actionDate',  $format, $timezone); }
	public function getEnteredByPerson() { return parent::getPersonObject('enteredByPerson'); }
	public function getActionPerson()    { return parent::getPersonObject('actionPerson');    }

	public function setAction($s) { $this->data['action'] = trim($s); }
	public function setNotes ($s) { $this->data['notes']  = trim($s); }
	public function setEnteredDate($date) { parent::setDateData('enteredDate', $date); }
	public function setActionDate ($date) { parent::setDateData('actionDate',  $date); }
	public function setEnteredByPerson($person) { parent::setPersonData('enteredByPerson', $person); }
	public function setActionPerson   ($person) { parent::setPersonData('actionPerson',    $person); }

	/**
	 * @param array $post
	 */
	public function set($post)
	{
		$this->setAction($post['action']);
		$this->setActionDate($post['actionDate']);
		$this->setEnteredByPerson($_SESSION['USER']);
		$this->setActionPerson($_SESSION['USER']);
		$this->setNotes($post['notes']);
	}

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
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
				$enteredByPerson = $enteredByPerson ? $enteredByPerson->getFullname() : APPLICATION_NAME;

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

		if ($actionPerson
			&& (!$enteredByPerson
				|| "{$enteredByPerson->getId()}" != "{$actionPerson->getId()}")) {

			$actionPerson->sendNotification(
				"$url\n\n{$this->getDescription()}\n\n{$this->getNotes()}",
				APPLICATION_NAME.' '.$this->getAction(),
				$enteredByPerson
			);
		}
	}
}
