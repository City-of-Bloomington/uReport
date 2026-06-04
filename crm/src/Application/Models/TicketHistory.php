<?php
/**
 * @copyright 2011-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Application\ActiveRecord;
use Application\Database;
use Application\Template;

class TicketHistory extends ActiveRecord
{
    public const TABLENAME = 'ticketHistory';

    protected $enteredByPerson;
    protected $actionPerson;

    protected $ticket;
    protected $action;

    public function __construct($id=null)
    {
        if ($id) {
            if (is_array($id)) {
                $this->exchangeArray($id);
            }
            else {
                $sql = "select * from ticketHistory where id=?";
                $result = Database::query($sql, [$id]);
                if (count($result)) {
                    $this->exchangeArray($result[0]);
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
     */
    public function exchangeArray(array $data)
    {
        parent::exchangeArray($data);

        $this->enteredByPerson = null;
        $this->actionPerson    = null;
        $this->ticket          = null;
        $this->action          = null;
    }

    /**
     * @throws \Exception
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
    public function getNotes()              { return parent::get('notes');              }
    public function getEnteredByPerson_id() { return parent::get('enteredByPerson_id'); }
    public function getActionPerson_id()    { return parent::get('actionPerson_id');    }
    public function getAction_id()          { return parent::get('action_id');          }
    public function getEnteredDate(?string $f=null, ?\DateTimeZone $tz=null) { return parent::getDateData('enteredDate', $f, $tz); }
    public function getActionDate (?string $f=null, ?\DateTimeZone $tz=null) { return parent::getDateData('actionDate',  $f, $tz); }
    public function getEnteredByPerson() { return parent::getForeignKeyObject(__namespace__.'\Person', 'enteredByPerson_id'); }
    public function getActionPerson()    { return parent::getForeignKeyObject(__namespace__.'\Person', 'actionPerson_id');    }
    public function getAction()          { return parent::getForeignKeyObject(__namespace__.'\Action', 'action_id');          }
    public function getData()              { $d = parent::get('data');              return $d ? json_decode($d, true) : null; }
    public function getSentNotifications() { $n = parent::get('sentNotifications'); return $n ? json_decode($n) : null; }

    public function setNotes ($s) { parent::set('notes',  $s); }
    public function setEnteredDate($d) { parent::setDateData('enteredDate', $d); }
    public function setActionDate ($d) { parent::setDateData('actionDate',  $d); }
    public function setEnteredByPerson_id($id)    { parent::setForeignKeyField( __namespace__.'\Person', 'enteredByPerson_id', $id); }
    public function setActionPerson_id   ($id)    { parent::setForeignKeyField( __namespace__.'\Person', 'actionPerson_id',    $id); }
    public function setAction_id         ($id)    { parent::setForeignKeyField( __namespace__.'\Action', 'action_id',          $id); }
    public function setEnteredByPerson(Person $p) { parent::setForeignKeyObject(__namespace__.'\Person', 'enteredByPerson_id', $p);  }
    public function setActionPerson   (Person $p) { parent::setForeignKeyObject(__namespace__.'\Person', 'actionPerson_id',    $p);  }
    public function setAction         (Action $o) { parent::setForeignKeyObject(__namespace__.'\Action', 'action_id',          $o);  }
    public function setData(?array $data=null) { parent::set('data', json_encode($data)); }

    public function getTicket_id() { return parent::get('ticket_id');          }
    public function getTicket()    { return parent::getForeignKeyObject(__namespace__.'\Ticket', 'ticket_id'); }
    public function setTicket_id($id)     { parent::setForeignKeyField( __namespace__.'\Ticket', 'ticket_id', $id); }
    public function setTicket(Ticket $o)  { parent::setForeignKeyObject(__namespace__.'\Ticket', 'ticket_id', $o); }

    public function handleUpdate(array $post)
    {
        $this->setTicket_id ($post['ticket_id']);
        $this->setAction_id ($post['action_id'] );
        $this->setActionDate($post['actionDate']->format(ActiveRecord::MYSQL_DATETIME_FORMAT));
        $this->setNotes     ($post['notes']     );
        $this->setEnteredByPerson($_SESSION['USER']);
        $this->setActionPerson   ($_SESSION['USER']);
    }

    //----------------------------------------------------------------
    // Custom Functions
    //----------------------------------------------------------------
    public function getDescription(Template $template): ?string
    {
        $action = $this->getAction();
        if ($action) {
            return $this->renderVariables(
                $action->getDescription(),
                $template
            );
        }
        return null;
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
     */
    public function renderVariables(string $message, Template $template): string
    {
        $placeholders = [
            'enteredByPerson'=> $this->getEnteredByPerson_id() ? $this->getEnteredByPerson()->anonymizeCitizenName($template) : $template->_('anonymous'),
            'actionPerson'   => $this->getActionPerson_id()    ? $this->getActionPerson   ()->anonymizeCitizenName($template) : $template->_('anonymous'),
            'ticket_id'      => $this->getTicket_id(),
            'enteredDate'    => $this->getEnteredDate(DATETIME_FORMAT),
            'actionDate'     => $this->getActionDate (DATETIME_FORMAT)
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
            $message = preg_replace("/\{$key\}/", $value ?? '', $message);
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
        if (defined('NOTIFICATIONS_ENABLED') && NOTIFICATIONS_ENABLED) {
            $ticket   = $this->getTicket();
            $category = $ticket->getCategory();
            $url      = $ticket->getUrl();
            $action   = $this->getAction();
            $notes    = $this->getNotes();
            $autoResponse  = $category->responseTemplateForAction($action);

            if ($autoResponse || $notes) {
                $template = new Template('email', 'txt');

                $subject = APPLICATION_NAME." {$template->_('ticket')} #{$ticket->getId()}";

                if ($autoResponse) {
                    $response   = $this->renderVariables($autoResponse->getTemplate(), $template);
                    $emailReply = $autoResponse->getReplyEmail();
                }
                else {
                    $response   = '';
                    $emailReply = $category->getNotificationReplyEmail();
                }

                $block = new \Application\Block('ticketHistory/notification.inc', [
                    'ticket'            => $ticket,
                    'actionDescription' => $this->getDescription($template),
                    'autoResponse'      => $response,
                    'userComments'      => $notes
                ]);
                $message = $block->render('txt', $template);

                $notificationLog = new \stdClass();
                $notificationLog->message = $message;
                $notificationLog->people  = [];

                foreach ($ticket->getNotificationPeople() as $person) {
                    if ($category->allowsDisplay($person)) {
                        $person->sendNotification($message, $subject, $emailReply);
                        $notificationLog->people[] = (int)$person->getId();
                    }
                }
                if (count($notificationLog->people)) {
                    $sql = 'update ticketHistory set sentNotifications=? where id=?';
                    Database::execute($sql, [json_encode($notificationLog), $this->getId()]);
                }
            }
        }
    }
}
