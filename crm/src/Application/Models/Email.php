<?php
/**
 * @copyright 2013-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;
use Application\ActiveRecord;
use Application\Database;

class Email extends ActiveRecord
{
	protected $tablename = 'peopleEmails';
	protected $person;

	public static $LABELS = array('Work','Home','Other');

	/**
	 * Populates the object with data
	 *
	 * Passing in an associative array of data will populate this object without
	 * hitting the database.
	 *
	 * Passing in a scalar will load the data from the database.
	 * This will load all fields in the table as properties of this class.
	 * You may want to replace this with, or add your own extra, custom loading
	 *
	 * @param int|array $id
	 */
	public function __construct($id=null)
	{
		if ($id) {
			if (is_array($id)) {
                $this->exchangeArray($id);
			}
			else {
				$db = Database::getConnection();
				$sql = 'select * from peopleEmails where id=?';
                $result = $db->createStatement($sql)->execute([$id]);
                if (count($result)) {
                    $this->exchangeArray($result->current());
                }
                else {
                    throw new \Exception('emails/unknown');
                }
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->setLabel('Other');
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

        $this->person = null;
    }

	public function validate()
	{
        if (!self::isValidFormat($this->getEmail())) { throw new \Exception('email/invalidFormat'); }

		if (!$this->getLabel()) { $this->setLabel('Other'); }
		if (!$this->getPerson_id()) { throw new \Exception('missingRequiredFields'); }

		// Make sure there's at least one email used for notifications
		$notificationEmails = $this->getPerson()->getNotificationEmails();
		if (!count($notificationEmails)) { $this->setUsedForNotifications(true); }
		if  (count($notificationEmails) == 1) {
			$e = $notificationEmails->current();
			if ($e->getId()==$this->getId()) { $this->setUsedForNotifications(true); }
		}

		// Required for the MySQL null default handler to be used
		if (!$this->isUsedForNotifications()) {
			unset($this->data['usedForNotifications']);
		}
	}

	public function save()   { parent::save();   }
	public function delete()
	{
		$person = $this->getPerson();

		parent::delete();

		// If we delete the only email used for notifications,
		// we need to mark one of the other email addresses.
		$db = Database::getConnection();
		$result = $db->query('select count(*) as c from peopleEmails where usedForNotifications=1 and person_id=?')->execute([$person->getId()]);
		$row = $result->current();
		if (!$row['c']) {
			$list = $person->getEmails();
			if (count($list)) {
				$e = $list->current();
				$e->setUsedForNotifications(true);
				$e->save();
			}
		}

	}

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()    { return parent::get('id');    }
	public function getEmail() { return parent::get('email'); }
	public function getLabel() { return parent::get('label'); }

	public function setEmail($s) { parent::set('email', $s); }
	public function setLabel($s) { parent::set('label', $s); }

	public function getPerson_id() { return parent::get('person_id'); }
	public function getPerson()    { return parent::getForeignKeyObject(__namespace__.'\Person', 'person_id');      }
	public function setPerson_id($id)     { parent::setForeignKeyField (__namespace__.'\Person', 'person_id', $id); }
	public function setPerson(Person $p)  { parent::setForeignKeyObject(__namespace__.'\Person', 'person_id', $p);  }

	public function getUsedForNotifications() { return parent::get('usedForNotifications') ? true : false; }
	public function setUsedForNotifications($b)      { parent::set('usedForNotifications', $b ? 1 : 0); }

	public function handleUpdate($post)
	{
		$fields = array('email', 'label', 'person_id', 'usedForNotifications');
		foreach ($fields as $f) {
			if (isset($post[$f])) {
				$set = 'set'.ucfirst($f);
				$this->$set($post[$f]);
			}
		}
	}

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	public function __toString() { return "{$this->getEmail()}"; }

	/**
	 * Alias for ::getUsedForNotifications
	 *
	 * @return bool
	 */
	public function isUsedForNotifications() { return $this->getUsedForNotifications(); }

    public static function isValidFormat(string $email): bool
    {
        $regex = "/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/";
        return preg_match($regex, $email);
    }
}
