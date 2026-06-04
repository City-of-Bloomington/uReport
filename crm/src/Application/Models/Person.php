<?php
/**
 * @copyright 2009-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Application\ActiveRecord;
use Application\Database;
use Application\Template;

use Domain\Auth\ExternalIdentity;
use PHPMailer\PHPMailer\PHPMailer;

class Person extends ActiveRecord
{
    public const TABLENAME = 'people';

    protected $department;

    /**
     * Returns the matching Person object or null if not found
     */
    public static function findByUsername(string $username): ?Person
    {
        $sql = 'select * from people where username=?';
        $result = Database::query($sql, [$username]);
        if (count($result)) {
            return new Person($result[0]);
        }
        return null;
    }

    /**
     * Populates the object with data
     *
     * Passing in an associative array of data will populate this object without
     * hitting the database.
     *
     * Passing in a scalar will load the data from the database.
     * This will load all fields in the table as properties of this class.
     * You may want to replace this with, or add your own extra, custom loading
     */
    public function __construct($id=null)
    {
        if ($id) {
            if (is_array($id)) {
                $this->exchangeArray($id);
            }
            else {
                if (ActiveRecord::isId($id)) {
                    $sql = 'select * from people where id=?';
                }
                elseif (false !== strpos($id,'@')) {
                    $sql = "select p.* from people p
                            left join peopleEmails e on p.id=e.person_id
                            where email=?";
                }
                else {
                    $sql = 'select * from people where username=?';
                }

                $result = Database::query($sql, [$id]);
                if (count($result)) {
                    $this->exchangeArray($result[0]);
                }
                else {
                    throw new \Exception('people/unknown');
                }
            }
        }
        else {
            // This is where the code goes to generate a new, empty instance.
            // Set any default values for properties that need it here
        }
    }

    /**
     * When repopulating with fresh data, make sure to set default
     * values on all object properties.
     */
    public function exchangeArray(array $data)
    {
        parent::exchangeArray($data);

        $this->department = null;
    }

    /**
     * Throws an exception if anything's wrong
     *
     * @throws \Exception
     */
    public function validate()
    {
        // Check for required fields here.  Throw an exception if anything is missing.
        if ((!$this->getFirstname() && !$this->getLastname())
            && !$this->getOrganization()) {
            throw new \Exception('missingRequiredFields');
        }
    }

    public function save()
    {
        parent::save();
    }

    public function delete()
    {
        if ($this->getId()) {
            if ($this->hasTickets()) {
                throw new \Exception('people/personStillHasTickets');
            }

            Database::execute('delete from peopleAddresses where person_id=?', [$this->getId()]);
            Database::execute('delete from peoplePhones    where person_id=?', [$this->getId()]);
            Database::execute('delete from peopleEmails    where person_id=?', [$this->getId()]);

            parent::delete();
        }
    }

    /**
     * Removes all the user account related fields from this Person
     */
    public function deleteUserAccount()
    {
        $userAccountFields = [
            'username', 'password', 'role', 'department_id'
        ];
        foreach ($userAccountFields as $f) {
            $this->data[$f] = null;
        }
        $this->department = null;
    }


    //----------------------------------------------------------------
    // Generic Getters & Setters
    //----------------------------------------------------------------
    public function getFirstname()     { return parent::get('firstname');    }
    public function getMiddlename()    { return parent::get('middlename');   }
    public function getLastname()      { return parent::get('lastname');     }
    public function getOrganization()  { return parent::get('organization'); }

    public function setFirstname   ($s) { parent::set('firstname',    $s); }
    public function setMiddlename  ($s) { parent::set('middlename',   $s); }
    public function setLastname    ($s) { parent::set('lastname',     $s); }
    public function setOrganization($s) { parent::set('organization', $s); }

    public function getDepartment_id()    { return parent::get('department_id'); }
    public function getDepartment()       { return parent::getForeignKeyObject(__namespace__.'\Department', 'department_id');      }
    public function setDepartment_id($id)        { parent::setForeignKeyField (__namespace__.'\Department', 'department_id', $id); }
    public function setDepartment(Department $d) { parent::setForeignKeyObject(__namespace__.'\Department', 'department_id', $d);  }

    public function getUsername()             { return parent::get('username'); }
    public function getRole()                 { return parent::get('role');     }

    public function setUsername            ($s) { parent::set('username',             $s); }
    public function setRole                ($s) { parent::set('role',                 $s); }

    /**
     * Updates fields that are not associated with authentication
     */
    public function handleUpdate(array $post)
    {
        $fields = array(
            'firstname', 'middlename', 'lastname', 'organization'
        );
        foreach ($fields as $field) {
            if (isset($post[$field])) {
                $set = 'set'.ucfirst($field);
                $this->$set($post[$field]);
            }
        }
    }

    /**
     * Updates only the fields associated with authentication
     */
    public function handleUpdateUserAccount(array $post)
    {
        $this->handleUpdate($post);

        $fields = array('department_id', 'username', 'role');
        foreach ($fields as $f) {
            if (isset($post[$f])) {
                $set = 'set'.ucfirst($f);
                $this->$set($post[$f]);
            }
        }

        $id = $this->getExternalIdentity();
        if ($id) {
            $this->populateFromExternalIdentity($id);
        }
    }

    public function getExternalIdentity(): ?ExternalIdentity
    {
        if ($this->getUsername()) {
            global $DIRECTORY_CONFIG;

            $class = $DIRECTORY_CONFIG['Employee']['classname'];
            $dir   = new $class($DIRECTORY_CONFIG['Employee']);
            return $dir->identify($this->getUsername());
        }
        return null;
    }

    /**
     * Checks if the user is supposed to have access to the resource
     *
     * This is implemented by checking against a Laminas Acl object
     * The Laminas Acl should be created in bootstrap.inc
     */
    public static function isAllowed(string $resource, ?string $action=null): bool
    {
        global $ACL;
        $role = 'Anonymous';
        if (isset($_SESSION['USER']) && $_SESSION['USER']->getRole()) {
            $role = $_SESSION['USER']->getRole();
        }
        return $ACL->isAllowed($role, $resource, $action);
    }

    public function getPhones(): array
    {
        $out = [];
        $sql = 'select * from peoplePhones where person_id=?';
        $res = Database::query($sql, [$this->getId()]);
        foreach ($res as $r) { $out[] = new Phone($r); }
        return $out;
    }

    public function getEmails(): array
    {
        $out = [];
        $sql = 'select * from peopleEmails where person_id=?';
        $res = Database::query($sql, [$this->getId()]);
        foreach ($res as $r) {
            $out[] = new Email($r);
        }
        return $out;
    }

    public function getNotificationEmails(): array
    {
        $out = [];
        $sql = 'select * from peopleEmails where person_id=? and usedForNotifications=1';
        $res = Database::query($sql, [$this->getId()]);
        foreach ($res as $r) { $out[] = new Email($r); }
        return $out;
    }

    public function getAddresses(): array
    {
        $out = [];
        $sql = 'select * from peopleAddresses where person_id=?';
        $res = Database::query($sql, [$this->getId()]);
        foreach ($res as $r) { $out[] = new Address($r); }
        return $out;
    }

    public function getFullname(): string
    {
        return ($this->getFirstname() || $this->getLastname())
            ? "{$this->getFirstname()} {$this->getLastname()}"
            : $this->getOrganization();
    }

    /**
     * Returns the person name only if $person is city staff
     * or if the current user is permitted to view all personal info.
     */
    public function anonymizeCitizenName(Template $t): string
    {
        return ($this->getUsername() || Person::isAllowed('people', 'view'))
            ? $this->getFullname()
            : $t->_('anonymous');
    }

    public function getURL(): ?string
    {
        if ($this->getId()) {
            return BASE_URL."/people/view?person_id={$this->getId()}";
        }
        return null;
    }

    /**
     * @param string $personFieldname The field in Ticket that has this person embedded
     * @param array  $fields          Additional fields to filter the ticketList
     */
    public function getTickets(string $personFieldname, ?array $fields=null): array
    {
        if ($this->getId()) {
            $field = $personFieldname.'Person_id';
            if (is_array($fields)) {
                $search = $fields;
                $search[$field] = $this->getId();
            }
            else {
                $search = [$field=>$this->getId()];
            }
            $table = new TicketTable();
            $list  = $table->find($search);
            return $list['rows'];
        }
        return [];
    }

    /**
     * Returns true if this person's ID is associated with any fields in the ticket records
     */
    public function hasTickets(): bool
    {
        $id = (int)$this->getId();
        if ($id) {
            $pdo = Database::getConnection();
            // This query is written as a Union for speed
            // A Union is the only way to use the indexes for this query
            $sql = "(select t.id from tickets t
                    where t.enteredByPerson_id =$id
                       or t.assignedPerson_id  =$id
                       or t.reportedByPerson_id=$id
                    limit 1)
                    union all
                    (select h.ticket_id from ticketHistory h
                    where h.enteredByPerson_id=$id
                       or h.actionPerson_id=$id
                    limit 1)
                    union all
                    (select m.ticket_id from media m
                    where m.person_id=$id
                    limit 1)";
            $result = Database::query($sql, []);
            return count($result) ? true : false;
        }
        return false;
    }

    public function sendNotification(string $message, ?string $subject=null, ?string $replyTo=null)
    {
        if (defined('NOTIFICATIONS_ENABLED') && NOTIFICATIONS_ENABLED) {
            if (!$subject) {
                $subject = APPLICATION_NAME.' Notification';
            }

            $mail = new PHPMailer(true);
            $mail->isHTML(false);
            $mail->isSMTP();
            $mail->Host        = SMTP_HOST;
            $mail->Port        = SMTP_PORT;
            $mail->SMTPAutoTLS = false;
            $mail->Subject     = $subject;
            $mail->Body        = $message;
            $mail->setFrom('no-reply@'.BASE_HOST, APPLICATION_NAME);

            foreach ($this->getNotificationEmails() as $email) {
                if (Email::isValidFormat($email->getEmail())) {
                    $mail->addAddress($email->getEmail());
                    $mail->send();
                }
                $mail->clearAddresses();
            }
        }
    }

    /**
     * Returns the array of distinct field values for People records
     *
     * This is primarily used to populate autocomplete lists for search forms
     * Make sure to keep this function as fast as possible
     */
    public static function getDistinct(string $fieldname, ?string $query=null): array
    {
        $fieldname = trim($fieldname);
        $db = Database::getConnection();

        $validFields = array('firstname', 'lastname', 'organization');
        if (in_array($fieldname, $validFields)) {
            $sql = "select distinct $fieldname from people where $fieldname like ?";
        }
        elseif ($fieldname == 'email') {
            $sql = "select distinct email from peopleEmails where email like ?";
        }
        else {
            return [];
        }
        $result = Database::query($sql, ["$query%"]);
        $o = [];
        foreach ($result as $row) { $o[] = $row[$fieldname]; }
        return $o;
    }

    public function populateFromExternalIdentity(ExternalIdentity $id)
    {
        if (!$this->getFirstname() && $id->firstname) { $this->setFirstname($id->firstname); }
        if (!$this->getLastname()  && $id->lastname ) { $this->setLastname ($id->lastname ); }

        // We're going to be adding email and phone records for this person.
        // We have to save the person record before we can do the foreign keys.
        if (!$this->getId()) { $this->save(); }

        $list = $this->getEmails();
        if (!count($list) && $id->email) {
            $email = new Email();
            $email->setPerson($this);
            $email->setEmail($id->email);
            $email->save();
        }
        $list = $this->getPhones();
        if (!count($list) && $id->phone) {
            $phone = new Phone();
            $phone->setPerson($this);
            $phone->setNumber($id->phone);
            $phone->save();
        }
        $list = $this->getAddresses();
        if (!count($list) && $id->address) {
            $address = new Address();
            $address->setPerson($this);
            $address->setAddress($id->address);
            $address->setCity   ($id->city   );
            $address->setState  ($id->state  );
            $address->setZip    ($id->zip    );
            $address->save();
        }
    }

    /**
     * Transfers all data from a person, then deletes that person
     *
     * This person will end up containing all information from both people
     * I took care to make sure to update the search index as well
     * as the database.
     *
     * @param Person $person
     */
    public function mergeFrom(Person $person)
    {
        if ($this->getId() && $person->getId()) {
            if($this->getId() == $person->getId()){
                // can not merge same person throw exception
                throw new \Exception('people/mergerNotAllowed');
            }

            // Look up all the tickets we're about to modify
            // We need to remember them so we can update the search
            // index after we've updated the database
            $id  = (int)$person->getId();
            $sql = "select distinct t.id from tickets t
                    left join ticketHistory th on t.id=th.ticket_id
                    left join media          m on t.id= m.ticket_id
                    where ( t.enteredByPerson_id=$id or t.assignedPerson_id=$id or t.reportedByPerson_id=$id)
                       or (th.enteredByPerson_id=$id or  th.actionPerson_id=$id)
                       or m.person_id=$id";
            $result = Database::query($sql, []);
            $ticketIds = [];
            foreach ($result as $row) {
                $ticketIds[] = $row['id'];
            }

            $pdo = Database::getConnection();
            $pdo->beginTransaction();
            try {
                // These are all the database fields that hit the Solr index
                Database::execute('update media         set           person_id=? where           person_id=?', [$this->getId(), $person->getId()]);
                Database::execute('update tickets       set reportedByPerson_id=? where reportedByPerson_id=?', [$this->getId(), $person->getId()]);
                Database::execute('update ticketHistory set  enteredByPerson_id=? where  enteredByPerson_id=?', [$this->getId(), $person->getId()]);
                Database::execute('update ticketHistory set     actionPerson_id=? where     actionPerson_id=?', [$this->getId(), $person->getId()]);
                Database::execute('update tickets       set  enteredByPerson_id=? where  enteredByPerson_id=?', [$this->getId(), $person->getId()]);
                Database::execute('update tickets       set   assignedPerson_id=? where   assignedPerson_id=?', [$this->getId(), $person->getId()]);

                // Fields that don't hit the Solr index
                Database::execute('update clients         set contactPerson_id=? where contactPerson_id=?', [$this->getId(), $person->getId()]);
                Database::execute('update departments     set defaultPerson_id=? where defaultPerson_id=?', [$this->getId(), $person->getId()]);
                Database::execute('update peopleAddresses set        person_id=? where        person_id=?', [$this->getId(), $person->getId()]);
                Database::execute('update peoplePhones    set        person_id=? where        person_id=?', [$this->getId(), $person->getId()]);
                Database::execute('update peopleEmails    set        person_id=? where        person_id=?', [$this->getId(), $person->getId()]);

                Database::execute('delete from people where id=?', [$person->getId()]);
            }
            catch (\Exception $e) {
                $pdo->rollBack();
                throw($e);
            }
            $pdo->commit();

            foreach ($ticketIds as $id) {
                $search = new Search();
                $ticket = new Ticket($id);
                $search->add($ticket);
            }
        }
    }
}
