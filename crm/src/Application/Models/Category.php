<?php
/**
 * @copyright 2011-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;
use Application\ActiveRecord;
use Application\Database;

class Category extends ActiveRecord
{
    public const TABLENAME = 'categories';

    protected $autoCloseSubstatus;
    protected $department;
    protected $defaultPerson;
    protected $categoryGroup;

    private $displayPermissionLevelHasChanged = false;
    private $slaDaysHasChanged = false;

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
                $sql = ActiveRecord::isId($id)
                    ? 'select * from categories where id=?'
                    : 'select * from categories where name=?';
                $result = Database::query($sql, [$id]);
                if (count($result)) {
                    $this->exchangeArray($result[0]);
                }
                else {
                    throw new \Exception('categories/unknown');
                }
            }
        }
        else {
            // This is where the code goes to generate a new, empty instance.
            // Set any default values for properties that need it here
            $this->setActive(true);
            if (isset($_SESSION['USER']) && $_SESSION['USER']->getDepartment()) {
                $this->setDepartment($_SESSION['USER']->getDepartment());
            }
        }
    }

    /**
     * When repopulating with fresh data, make sure to set default
     * values on all object properties.
     */
    public function exchangeArray(array $data)
    {
        parent::exchangeArray($data);

        $this->department    = null;
        $this->categoryGroup = null;
        $this->displayPermissionLevelHasChanged = false;
    }

    /**
     * Throws an exception if anything's wrong
     *
     * @throws \Exception
     */
    public function validate()
    {
        if (!$this->getName())             { throw new \Exception('categories/missingName');  }
        if (!$this->getCategoryGroup_id()) { throw new \Exception('categories/missingGroup'); }
        if (!$this->getDepartment_id())    { throw new \Exception('categories/missingDepartment'); }
    }

    public function save() {
        $this->setLastModified('now');

        parent::save();

        if ($this->displayPermissionLevelHasChanged || $this->slaDaysHasChanged) {
            // Spawn a background process to reindex the search engine
            $cmd = 'php '.APPLICATION_HOME.'/scripts/solr/indexCategory.php '.SITE_HOME.' '.$this->getId();
            if ($this->slaDaysHasChanged) { $cmd .= ' open'; }

            shell_exec("nohup $cmd > /dev/null 2>&1 &");
        }
    }

    //----------------------------------------------------------------
    // Getters and Setters
    //----------------------------------------------------------------
    public function __toString()                { return parent::get('name');                   }
    public function getName()                   { return parent::get('name');                   }
    public function getDepartment_id()          { return parent::get('department_id');          }
    public function getDefaultPerson_id()       { return parent::get('defaultPerson_id');       }
    public function getCategoryGroup_id()       { return parent::get('categoryGroup_id');       }
    public function getDescription()            { return parent::get('description');            }
    public function getActive()                 { return parent::get('active');                 }
    public function getFeatured()               { return parent::get('featured');               }
    public function getPostingPermissionLevel() { return parent::get('postingPermissionLevel'); }
    public function getDisplayPermissionLevel() { return parent::get('displayPermissionLevel'); }
    public function getSlaDays(): ?int          { return $this->data['slaDays'] ?? null;        }
    public function getNotificationReplyEmail() { return parent::get('notificationReplyEmail'); }
    public function getAutoCloseIsActive()      { return parent::get('autoCloseIsActive');      }
    public function getAutoCloseSubstatus_id()  { return parent::get('autoCloseSubstatus_id');  }
    public function getAutoCloseSubstatus() { return parent::getForeignKeyObject(__namespace__.'\Substatus',     'autoCloseSubstatus_id'); }
    public function getDepartment()         { return parent::getForeignKeyObject(__namespace__.'\Department',    'department_id'        ); }
    public function getDefaultPerson()      { return parent::getForeignKeyObject(__namespace__.'\Person',        'defaultPerson_id'     ); }
    public function getCategoryGroup()      { return parent::getForeignKeyObject(__namespace__.'\CategoryGroup', 'categoryGroup_id'     ); }
    public function getLastModified(?string $format=null, ?\DateTimeZone $timezone=null) { return parent::getDateData('lastModified', $format, $timezone); }

    public function setName                  ($s) { parent::set('name',                  $s); }
    public function setDescription           ($s) { parent::set('description',           $s); }
    public function setActive                ($s) { $this->data['active'  ] = $s ? 1 : 0; }
    public function setFeatured              ($s) { $this->data['featured'] = $s ? 1 : 0; }
    public function setPostingPermissionLevel($s) { parent::set('postingPermissionLevel',$s); }
    public function setNotificationReplyEmail($s) { parent::set('notificationReplyEmail',$s); }
    public function setAutoCloseIsActive     ($b) { $this->data['autoCloseIsActive'] = $b ? 1 : 0; }
    public function setAutoCloseSubstatus_id($id)           { parent::setForeignKeyField( __namespace__.'\Substatus',     'autoCloseSubstatus_id', $id); }
    public function setDepartment_id        ($id)           { parent::setForeignKeyField( __namespace__.'\Department',    'department_id',         $id); }
    public function setDefaultPerson_id     ($id)           { parent::setForeignKeyField( __namespace__.'\Person',        'defaultPerson_id',      $id); }
    public function setCategoryGroup_id     ($id)           { parent::setForeignKeyField( __namespace__.'\CategoryGroup', 'categoryGroup_id',      $id); }
    public function setAutoCloseSubstatus(Substatus     $o) { parent::setForeignKeyObject(__namespace__.'\Substatus',     'autoCloseSubstatus_id', $o);  }
    public function setDepartment        (Department    $o) { parent::setForeignKeyObject(__namespace__.'\Department',    'department_id',         $o);  }
    public function setDefaultPerson     (Person        $o) { parent::setForeignKeyObject(__namespace__.'\Person',        'defaultPerson_id',      $o);  }
    public function setCategoryGroup     (CategoryGroup $o) { parent::setForeignKeyObject(__namespace__.'\CategoryGroup', 'categoryGroup_id',      $o);  }
    public function setLastModified($d) { parent::setDateData('lastModified', $d); }

    public function setDisplayPermissionLevel($s)
    {
        if ($this->getId()) {
            $s = trim($s);
            if (   $this->getDisplayPermissionLevel()
                && $this->getDisplayPermissionLevel() != $s) {
                $this->displayPermissionLevelHasChanged = true;
            }
        }
        parent::set('displayPermissionLevel',$s);
    }

    public function setSlaDays(?int $i=null)
    {
        if ($this->getId()) {
            if ($this->getSlaDays() != $i) {
                $this->slaDaysHasChanged = true;
            }
        }
        $this->data['slaDays'] = $i;
    }

    public function handleUpdate(array $post)
    {
        $fields = [
            'name', 'description', 'department_id', 'defaultPerson_id', 'categoryGroup_id',
            'postingPermissionLevel', 'displayPermissionLevel',
            'customFields', 'slaDays', 'notificationReplyEmail',
            'autoCloseIsActive', 'autoCloseSubstatus_id'
        ];
        foreach ($fields as $f) {
            $set = 'set'.ucfirst($f);
            $this->$set($post[$f]);
        }

        $this->setActive  (!empty($post['active'  ]) ? $post['active'  ] : false);
        $this->setFeatured(!empty($post['featured']) ? $post['featured'] : false);
    }

    //----------------------------------------------------------------
    // Custom Functions
    //----------------------------------------------------------------
    public function autoCloseIsActive(): bool { return $this->getAutoCloseIsActive() ? true : false; }
    public function isActive()         : bool { return $this->getActive()            ? true : false; }
    public function isFeatured()       : bool { return $this->getFeatured()          ? true : false; }

    /**
     * Event handler called from Ticket::handleAdd()
     *
     * Handles the autoClose and autoResponse sending
     */
    public function onTicketAdd(Ticket &$ticket)
    {
        if ($this->autoCloseIsActive()) {
            $ticket->handleChangeStatus([
                'status'      => 'closed',
                'substatus_id'=> $this->getAutoCloseSubstatus_id(),
                'notes'       => AUTO_CLOSE_COMMENT
            ]);
        }
    }

    /**
     * Returns a PHP array representing the description of custom fields
     *
     * The category holds the description of the custom fields desired
     * $customFields = [
     *        [ 'name'=>'','type'=>'','label'=>'','values'=>[] ]
     * ]
     * Name and Label are required.
     * Anything without a type will be rendered as type='text'
     * If type is select, radio, or checkbox, you must provide values
     *        for the user to choose from
     */
    public function getCustomFields(): ?array
    {
        $f = parent::get('customFields');
        return $f ? json_decode($f, true) : null;
    }

    /**
     * Loads a valid JSON string describing the custom fields
     *
     * The category holds the description of the custom fields desired
     * $json = [
     *        {'name':'','type':'','label':'','values':['',''])
     * ]
     * Name and Label are required.
     * Anything without a type will be rendered as type='text'
     * If type is select, radio, or checkbox, you must provide values
     *        for the user to choose from
     */
    public function setCustomFields(?string $json=null)
    {
        $json = trim($json);
        $customFields = '';
        if ($json) {
            $customFields = json_decode($json, true);
            if (is_array($customFields)) {
                $this->data['customFields'] = $json;
            }
            else {
                $message = json_last_error();
                if ($message != JSON_ERROR_NONE) {
                    throw new JSONException($message);
                }
            }
        }
        else {
            $this->data['customFields'] = null;
        }
    }

    public function allowsDisplay(?Person $person=null): bool
    {
        if (!$person) {
            return $this->getDisplayPermissionLevel()==='anonymous';
        }
        elseif ($person->getRole()!=='Staff' && $person->getRole()!=='Administrator') {
            return in_array(
                $this->getDisplayPermissionLevel(),
                ['public','anonymous']
            );
        }
        return true;
    }

    public function allowsPosting(?Person $person=null): bool
    {
        if (!$person) {
            return $this->getPostingPermissionLevel()==='anonymous';
        }
        elseif ($person->getRole()!=='Staff' && $person->getRole()!=='Administrator') {
            return in_array(
                $this->getPostingPermissionLevel(),
                ['public','anonymous']
            );
        }
        return true;
    }

    /**
     * Returns the most recent lastModified date from all categories
     */
    public static function getGlobalLastModifiedDate(?string $format=null, ?\DateTimeZone $timezone=null): ?string
    {
        $result = Database::query('select max(lastModified) as lastModified from categories', []);
        if ($result) {
            if ($format) {
                $date = new \DateTime($result[0]['lastModified']);
                if ($timezone) { $date->setTimezone($timezone); }
                return $date->format($format);
            }
            return $result[0]['lastModified'];
        }
        return null;
    }

    /**
     * Returns an array of templates, with the template ID as the key
     */
    public function getResponseTemplates(): array
    {
        $out = [];
        $sql = 'select * from category_action_responses where category_id=?';
        $res = Database::query($sql, [$this->getId()]);
        foreach ($res as $r) { $out[] = new ResponseTemplate($r); }
        return $out;
    }

    public function responseTemplateForAction(Action $a): ?ResponseTemplate
    {
        $sql = 'select * from category_action_responses where category_id=? and action_id=?';
        $res = Database::query($sql, [$this->getId(), $a->getId()]);
        if (count($res)) {
            return new ResponseTemplate($res[0]);
        }
        else {
            if ($a->getTemplate()) {
                $r = new ResponseTemplate();
                $r->setCategory($this);
                $r->setAction    ($a);
                $r->setTemplate  ($a->getTemplate());
                $r->setReplyEmail($a->getReplyEmail());
                return $r;
            }
        }
        return null;
    }
}

class JSONException extends \Exception
{
    public function __construct($message)
    {
        switch ($message) {
            case JSON_ERROR_NONE:
                $this->message = 'No errors';
            break;
            case JSON_ERROR_DEPTH:
                $this->message = 'Maximum stack depth exceeded';
            break;
            case JSON_ERROR_STATE_MISMATCH:
                $this->message = 'Underflow or the modes mismatch';
            break;
            case JSON_ERROR_CTRL_CHAR:
                $this->message = 'Unexpected control character found';
            break;
            case JSON_ERROR_SYNTAX:
                $this->message = 'Syntax error, malformed JSON';
            break;
            case JSON_ERROR_UTF8:
                $this->message = 'Malformed UTF-8 characters, possibly incorrectly encoded';
            break;
            default:
                $this->message = 'Unknown JSON error';
        }
    }
}
