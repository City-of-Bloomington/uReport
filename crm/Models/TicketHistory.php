<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;

class TicketHistory extends ActiveRecord
{
    protected $tablename = 'ticketHistory';

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
				$sql = "select * from ticketHistory where id=?";
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

		if (!$this->getTicket_id()) {
            if ($this->getIssue_id()) {
                $issue = $this->getIssue();
                $this->setTicket_id($issue->getTicket_id());
            }
            else {
                throw new \Exception('missingRequiredFields');
            }
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

	public function save()
	{
        parent::save();
        $this->sendNotifications();
    }
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
	public function getIssue_id () { return parent::get('issue_id');           }
	public function getTicket()    { return parent::getForeignKeyObject(__namespace__.'\Ticket', 'ticket_id'); }
	public function getIssue ()    { return parent::getForeignKeyObject(__namespace__.'\Issue',  'issue_id' ); }
	public function setTicket_id($id)     { parent::setForeignKeyField( __namespace__.'\Ticket', 'ticket_id', $id); }
	public function setIssue_id ($id)     { parent::setForeignKeyField( __namespace__.'\Issue',  'issue_id',  $id); }
	public function setTicket(Ticket $o)  { parent::setForeignKeyObject(__namespace__.'\Ticket', 'ticket_id', $o); }
	public function setIssue (Issue  $o)  { parent::setForeignKeyObject(__namespace__.'\Issue',  'issue_id',  $o); }

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

		if (!empty($post['ticket_id'])) { $this->setTicket_id($post['ticket_id']); }
		if (!empty($post['issue_id' ])) { $this->setIssue_id ($post['issue_id' ]); }
	}

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	/**
	 * Returns the parsed description
	 *
	 * @param Person $person The person to whom the description will be displayed
	 * @return string
	 */
	public function getDescription(Person $person=null)
	{
		$a = $this->getAction();
		if ($a) {
			return $this->renderVariables(
				$this->getAction()->getDescription(),
				$person
			);
		}
	}

	/**
	 * Substitutes actual data for placeholders in the message
	 *
	 * Variables are embedded in the message using curly braces.
	 * Example: This message has a {variable} in it
	 *
	 * Some of the possible variables are for peoples' names.
	 * We to be careful and only give out peoples' names to authorized users
	 * Make sure to call this function by sending in the user to
	 * whom you're displaying the information.
	 *
	 * @param string $message
	 * @param Person $person   The person to whom the message will be displayed
	 * @return string
	 */
	public function renderVariables($message, Person $person=null)
	{
        global $ZEND_ACL;

        $userCanViewPeople = $person
            ? $ZEND_ACL->isAllowed($person->getRole(), 'people', 'view')
            : Person::isAllowed('people', 'view');

        $placeholders = [
            'enteredByPerson'=> $this->getEnteredByPerson_id() ? $this->getEnteredByPerson()->getFullname() : '',
            'actionPerson'   => $this->getActionPerson_id()    ? $this->getActionPerson()   ->getFullname() : ''
        ];

		foreach ($placeholders as $key=>$value) {
            if (false !== strpos($key, 'Person') && !$userCanViewPeople) {
                $value = $this->_('labels.someone');
            }
			$message = preg_replace("/\{$key\}/", $value, $message);
		}
		return $message;
	}

	/**
	 * Send a notification to all people involved with the ticket
	 */
	public function sendNotifications()
	{
        $ticket   = $this->getTicket();
        $category = $ticket->getCategory();
        $url      = $ticket->getUrl();
        $action   = $this->getAction();

        $template = $category->responseTemplateForAction($action);
        $notes    = $this->getNotes();
        if ($template || $notes) {
            foreach ($ticket->getNotificationEmails() as $email) {
                $emailTo     = $email->getPerson();
                $description = $this->getDescription($emailTo);
                if ($template) {
                    $response   = $this->renderVariables($template->getTemplate(), $emailTo);
                    $emailReply = $template->getReplyEmail();
                }
                else {
                    $response   = '';
                    $emailReply = $category->getNotificationReplyEmail();
                }

                $emailTo->sendNotification(
                    "$url\n\n$description\n\n$response\n\n$notes",
                    APPLICATION_NAME.' '.$action,
                    $emailReply
                );
            }
        }
	}
}
