<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
abstract class History extends ActiveRecord
{
	protected $enteredByPerson;
	protected $actionPerson;

	protected $ticket;
	protected $issue;

	/**
	 * @param array $data
	 */
	public function __construct($id=null)
	{
		if ($id) {
			if (is_array($id)) {
				$result = $id;
			}
			else {
				$zend_db = Database::getConnection();
				$sql = "select * from {$this->tablename} where id=?";
				$result = $zend_db->fetchRow($sql, array($id));
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->setEnteredDate('now');
			$this->setActionDate ('now');
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

		$id_field = $this->tablename == 'ticketHistory' ? 'ticket_id' : 'issue_id';
		if (!$this->data[$id_field]) {
			throw new Exception('missingRequiredFields');
		}


		if (!$this->data['enteredDate']) {
			$this->setEnteredDate('now');
		}

		if (!$this->data['actionDate']) {
			$this->setActionDate('now');
		}

		if (isset($_SESSION['USER'])) {
			if (!isset($this->data['enteredByPerson_id'])) {
				$this->setEnteredByPerson($_SESSION['USER']);
			}
			if (!isset($this->data['actionPerson_id'])) {
				$this->setActionPerson($_SESSION['USER']);
			}
		}
	}

	public function save()   { parent::save();   }
	public function delete() { parent::delete(); }

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()                 { return parent::get('id');                 }
	public function getAction()             { return parent::get('action');             }
	public function getNotes()              { return parent::get('notes');              }
	public function getEnteredByPerson_id() { return parent::get('enteredByPerson_id'); }
	public function getActionPerson_id()    { return parent::get('actionPerson_id');    }
	public function getEnteredDate($f=null, DateTimeZone $tz=null) { return parent::getDateData('enteredDate', $f, $tz); }
	public function getActionDate ($f=null, DateTimeZone $tz=null) { return parent::getDateData('actionDate',  $f, $tz); }
	public function getEnteredByPerson()    { return parent::getForeignKeyObject(  'Person', 'enteredByPerson_id');      }
	public function getActionPerson()       { return parent::getForeignKeyObject(  'Person', 'actionPerson_id');         }

	public function setAction($s) { parent::set('action', $s); }
	public function setNotes ($s) { parent::set('notes',  $s); }
	public function setEnteredDate($d) { parent::setDateData('enteredDate', $d); }
	public function setActionDate ($d) { parent::setDateData('actionDate',  $d); }
	public function setEnteredByPerson_id($id)    { parent::setForeignKeyField( 'Person', 'enteredByPerson_id', $id); }
	public function setActionPerson_id   ($id)    { parent::setForeignKeyField( 'Person', 'actionPerson_id',    $id); }
	public function setEnteredByPerson(Person $p) { parent::setForeignKeyObject('Person', 'enteredByPerson_id', $p);  }
	public function setActionPerson   (Person $p) { parent::setForeignKeyObject('Person', 'actionPerson_id',    $p);  }

	// History is either for a Ticket or an Issue
	public function getTicket_id() { return parent::get('ticket_id');          }
	public function getIssue_id()  { return parent::get('issue_id');           }
	public function setTicket_id($id) { parent::setForeignKeyField('Ticket', 'ticket_id', $id); }
	public function setIssue_id ($id) { parent::setForeignKeyField('Issue',  'issue_id',  $id); }
	public function setTicket(Ticket $o) { parent::setForeignKeyObject('Ticket', 'ticket_id', $o); }
	public function setIssue (Issue  $o) { parent::setForeignKeyObject('Issue',  'issue_id',  $o); }

	/**
	 * @param array $post
	 */
	public function handleUpdate($post)
	{
		$this->setAction($post['action']);
		$this->setActionDate($post['actionDate']);
		$this->setEnteredByPerson($_SESSION['USER']);
		$this->setActionPerson($_SESSION['USER']);
		$this->setNotes($post['notes']);

		$this->tablename == 'ticketHistory'
			? $this->setTicket_id($post['ticket_id'])
			: $this->setIssue_id($post['issue_id']);
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
		$actions = new ActionList();
		$actions->find();
		foreach ($actions as $action) {
			if ($action->getName() == $this->getAction()) {
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
		$enteredByPerson = $this->getEnteredByPerson();
		$actionPerson    = $this->getActionPerson();

		$url = $ticket ? $ticket->getURL() : '';

		if ($actionPerson
			&& (!$enteredByPerson
				|| $enteredByPerson->getId() != $actionPerson->getId())) {

			$actionPerson->sendNotification(
				"$url\n\n{$this->getDescription()}\n\n{$this->getNotes()}",
				APPLICATION_NAME.' '.$this->getAction(),
				$enteredByPerson
			);
		}
	}
}
