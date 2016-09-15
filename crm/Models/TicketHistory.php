<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;
use Blossom\Classes\Template;

class TicketHistory extends ActiveRecord
{
    protected $tablename = 'ticketHistory';

	protected $enteredByPerson;
	protected $actionPerson;

	protected $ticket;
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
					throw new \Exception('ticketHistory/unknown');
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
        $this->action          = null;
    }

	/**
	 * Throws an exception if anything's wrong
	 *
	 * @throws Exception $e
	 */
	public function validate()
	{
		if (!$this->getAction())    { throw new \Exception('ticketHistory/missingAction'); }
		if (!$this->getTicket_id()) { throw new \Exception('missingRequiredFields'); }

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
	public function getData() { return json_decode(parent::get('data'), true); }

	public function setNotes ($s) { parent::set('notes',  $s); }
	public function setEnteredDate($d) { parent::setDateData('enteredDate', $d); }
	public function setActionDate ($d) { parent::setDateData('actionDate',  $d); }
	public function setEnteredByPerson_id($id)    { parent::setForeignKeyField( __namespace__.'\Person', 'enteredByPerson_id', $id); }
	public function setActionPerson_id   ($id)    { parent::setForeignKeyField( __namespace__.'\Person', 'actionPerson_id',    $id); }
	public function setAction_id         ($id)    { parent::setForeignKeyField( __namespace__.'\Action', 'action_id',          $id); }
	public function setEnteredByPerson(Person $p) { parent::setForeignKeyObject(__namespace__.'\Person', 'enteredByPerson_id', $p);  }
	public function setActionPerson   (Person $p) { parent::setForeignKeyObject(__namespace__.'\Person', 'actionPerson_id',    $p);  }
	public function setAction         (Action $o) { parent::setForeignKeyObject(__namespace__.'\Action', 'action_id',          $o);  }
	public function setData(array $data=null) { parent::set('data', json_encode($data)); }

	public function getTicket_id() { return parent::get('ticket_id');          }
	public function getTicket()    { return parent::getForeignKeyObject(__namespace__.'\Ticket', 'ticket_id'); }
	public function setTicket_id($id)     { parent::setForeignKeyField( __namespace__.'\Ticket', 'ticket_id', $id); }
	public function setTicket(Ticket $o)  { parent::setForeignKeyObject(__namespace__.'\Ticket', 'ticket_id', $o); }

	/**
	 * @param array $post
	 */
	public function handleUpdate($post)
	{
		$this->setAction_id ($post['action_id'] );
		$this->setActionDate($post['actionDate']->format(ActiveRecord::MYSQL_DATETIME_FORMAT));
		$this->setNotes     ($post['notes']     );
		$this->setEnteredByPerson($_SESSION['USER']);
		$this->setActionPerson   ($_SESSION['USER']);
		$this->setTicket_id($post['ticket_id']);
	}

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	/**
	 * Returns the parsed description
	 *
	 * @param Template $template  The template being used for output formatting
	 * @param Person   $person    The person to whom the message will be displayed
	 * @return string
	 */
	public function getDescription(Template $template, Person $person=null)
	{
		$a = $this->getAction();
		if ($a) {
			return $this->renderVariables(
				$this->getAction()->getDescription(),
				$template,
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
	 * @param string   $message
	 * @param Template $template  The template being used for output formatting
	 * @param Person   $person    The person to whom the message will be displayed
	 * @return string
	 */
	public function renderVariables($message, Template $template, Person $person=null)
	{
        $placeholders = [
            'enteredByPerson'=> $this->getEnteredByPerson_id() ? $this->getEnteredByPerson()->getFullname() : '',
            'actionPerson'   => $this->getActionPerson_id()    ? $this->getActionPerson()   ->getFullname() : '',
            'ticket_id'      => $this->getTicket_id(),
            'enteredDate'    => $this->getEnteredDate(DATE_FORMAT),
            'actionDate'     => $this->getActionDate (DATE_FORMAT)
        ];
        $data = $this->getData();
        if ($data) {
            $dataGroups = [];
            $actionName = $this->getAction()->getName();
            switch ($actionName) {
                case Action::DUPLICATED:
                    $dataGroups[] = Action::DUPLICATED;
                break;

                case Action::CHANGED_CATEGORY:
                case Action::CHANGED_LOCATION:
                    $dataGroups[] = 'original';
                    $dataGroups[] = 'updated';
                break;
            }

            #$dataGroups = ['original', 'updated'];

            foreach ($dataGroups as $type) {
                if (!empty(  $data[$type])) {
                    foreach ($data[$type] as $key => $value) {
                        // Convert any _id values
                        if ($key === 'ticket_id') {
                            $uri = BASE_URI."/tickets/view?ticket_id=$value";
                            $value = "<a href=\"$uri\">$value</a>";
                        }
                        elseif (false !== strpos($key, '_id')) {
                            $value = $template::escape(Search::getDisplayName($key, $value));
                        }
                        $placeholders["$type:$key"] = $value;
                    }
                }
            }
        }

		foreach ($placeholders as $key=>$value) {
			$message = preg_replace("/\{$key\}/", $value, $message);
		}
		return $message;
	}

	/**
	 * Send a notification to all people involved with the ticket
	 *
	 * Right now, this is only via email
	 */
	public function sendNotifications()
	{
        $ticket   = $this->getTicket();
        $category = $ticket->getCategory();
        $url      = $ticket->getUrl();
        $action   = $this->getAction();

        $message  = $category->responseTemplateForAction($action);
        $notes    = $this->getNotes();
        if ($message || $notes) {
            $template = new Template('email', 'txt');

            $subject = APPLICATION_NAME." {$template->_('ticket')} #{$ticket->getId()}";

            foreach ($ticket->getNotificationPeople() as $person) {
                if ($category->allowsDisplay($person)) {
                    if ($message) {
                        $response   = $this->renderVariables($message->getTemplate(), $template, $person);
                        $emailReply = $message->getReplyEmail();
                    }
                    else {
                        $response   = '';
                        $emailReply = $category->getNotificationReplyEmail();
                    }

                    $description = $this->getDescription($template, $person);
                    $block = new \Blossom\Classes\Block('notifications/history.inc', [
                        'ticket'            => $ticket,
                        'actionDescription' => $description,
                        'autoResponse'      => $response,
                        'userComments'      => $notes
                    ]);
                    $person->sendNotification($block->render('txt', $template), $subject, $emailReply);
                }
            }
        }
	}
}
