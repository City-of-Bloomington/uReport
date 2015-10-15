<?php
/**
 * @copyright 2011-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;
use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;

abstract class History extends ActiveRecord
{
	protected $enteredByPerson;
	protected $actionPerson;

	protected $ticket;
	protected $issue;
	protected $action;

	/**
	 * @param array $data
	 */
	public function __construct($id=null)
	{
		if ($id) {
			if (is_array($id)) {
                $this->exchangeArray($id);
			}
			else {
				$zend_db = Database::getConnection();
				$sql = "select * from {$this->tablename} where id=?";
				$result = $zend_db->createStatement($sql)->execute([$id]);
				if (count($result)) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('history/unknownHistory');
				}
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
     * When repopulating with fresh data, make sure to set default
     * values on all object properties.
     *
     * @Override
     * @param array $data
     */
    public function exchangeArray($data)
    {
        parent::exchangeArray($data);

        $this->enteredByPerson = null;
        $this->actionPerson    = null;
        $this->ticket          = null;
        $this->issue           = null;
        $this->action          = null;
    }

	/**
	 * Throws an exception if anything's wrong
	 *
	 * @throws Exception $e
	 */
	public function validate()
	{
		if (!$this->getAction()) {
			throw new \Exception('history/missingAction');
		}

		$id_field = $this->tablename == 'ticketHistory' ? 'ticket_id' : 'issue_id';
		if (!$this->data[$id_field]) {
			throw new \Exception('missingRequiredFields');
		}

		if (!$this->data['enteredDate']) { $this->setEnteredDate('now'); }
		if (!$this->data['actionDate'] ) { $this->setActionDate ('now'); }

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
	public function getNotes()              { return parent::get('notes');              }
	public function getEnteredByPerson_id() { return parent::get('enteredByPerson_id'); }
	public function getActionPerson_id()    { return parent::get('actionPerson_id');    }
	public function getAction_id()          { return parent::get('action_id');          }
	public function getEnteredDate($f=null, \DateTimeZone $tz=null) { return parent::getDateData('enteredDate', $f, $tz); }
	public function getActionDate ($f=null, \DateTimeZone $tz=null) { return parent::getDateData('actionDate',  $f, $tz); }
	public function getEnteredByPerson() { return parent::getForeignKeyObject(__namespace__.'\Person', 'enteredByPerson_id'); }
	public function getActionPerson()    { return parent::getForeignKeyObject(__namespace__.'\Person', 'actionPerson_id');    }
	public function getAction()          { return parent::getForeignKeyObject(__namespace__.'\Action', 'action_id');          }

	public function setNotes ($s) { parent::set('notes',  $s); }
	public function setEnteredDate($d) { parent::setDateData('enteredDate', $d); }
	public function setActionDate ($d) { parent::setDateData('actionDate',  $d); }
	public function setEnteredByPerson_id($id)    { parent::setForeignKeyField( __namespace__.'\Person', 'enteredByPerson_id', $id); }
	public function setActionPerson_id   ($id)    { parent::setForeignKeyField( __namespace__.'\Person', 'actionPerson_id',    $id); }
	public function setAction_id         ($id)    { parent::setForeignKeyField( __namespace__.'\Action', 'action_id',          $id); }
	public function setEnteredByPerson(Person $p) { parent::setForeignKeyObject(__namespace__.'\Person', 'enteredByPerson_id', $p);  }
	public function setActionPerson   (Person $p) { parent::setForeignKeyObject(__namespace__.'\Person', 'actionPerson_id',    $p);  }
	public function setAction         (Action $o) { parent::setForeignKeyObject(__namespace__.'\Action', 'action_id',          $o);  }

	// History is either for a Ticket or an Issue
	public function getTicket_id() { return parent::get('ticket_id');          }
	public function getIssue_id()  { return parent::get('issue_id');           }
	public function setTicket_id($id)    { parent::setForeignKeyField( __namespace__.'\Ticket', 'ticket_id', $id); }
	public function setIssue_id ($id)    { parent::setForeignKeyField( __namespace__.'\Issue',  'issue_id',  $id); }
	public function setTicket(Ticket $o) { parent::setForeignKeyObject(__namespace__.'\Ticket', 'ticket_id', $o); }
	public function setIssue (Issue  $o) { parent::setForeignKeyObject(__namespace__.'\Issue',  'issue_id',  $o); }

	/**
	 * @param array $post
	 */
	public function handleUpdate($post)
	{
		$this->setAction_id ($post['action_id'] );
		$this->setActionDate($post['actionDate']);
		$this->setNotes     ($post['notes']     );
		$this->setEnteredByPerson($_SESSION['USER']);
		$this->setActionPerson   ($_SESSION['USER']);

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
		$ep = $this->getEnteredByPerson_id() ? $this->getEnteredByPerson()->getFullname() : '';
		$ap = $this->getActionPerson_id()    ? $this->getActionPerson()   ->getFullname() : '';

		$a = $this->getAction();
		if ($a) {
			return $this->parseDescription(
				$this->getAction()->getDescription(),
				array(
					'enteredByPerson'=> $ep,
					'actionPerson'   => $ap
				)
			);
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
	public function parseDescription($description, $placeholders)
	{
		foreach ($placeholders as $key=>$value) {
			$description = preg_replace("/\{$key\}/", $value, $description);
		}
		return $description;
	}

	/**
	 * Send a notification of this action to the actionPerson
	 *
	 * Does not send if the enteredByPerson and actionPerson are the same person
	 * @param Ticket $ticket
	 */
	public function sendNotification(Ticket $ticket=null)
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
				$ticket->getCategory()->getNotificationReplyEmail()
			);
		}
	}
}
