<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
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
				$result = $id;
			}
			else {
				$zend_db = Database::getConnection();
				$sql = 'select * from peopleEmails where id=?';
				$result = $zend_db->fetchRow($sql, array($id));
			}

			if ($result) {
				$this->data = $result;
			}
			else {
				throw new Exception('emails/unknownEmail');
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->setLabel('Other');
		}
	}

	public function validate()
	{
		if (!$this->getLabel()) { $this->setLabel('Other'); }
		if (!$this->getPerson_id()) { throw new Exception('phones/missingPerson'); }

		// Make sure there's at least one email used for notifications
		$notificationEmails = $this->getPerson()->getNotificationEmails();
		if (!count($notificationEmails)) { $this->setUsedForNotifications(true); }
		if  (count($notificationEmails) == 1) {
			$e = $notificationEmails[0];
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
		$notificationEmails = new EmailList(array('person_id'=>$person->getId(), 'usedForNotifications'=>1));
		if (!count($notificationEmails)) {
			$list = $person->getEmails();
			if (count($list)) {
				$e = $list[0];
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
	public function getPerson()    { return parent::getForeignKeyObject('Person', 'person_id');      }
	public function setPerson_id($id)     { parent::setForeignKeyField ('Person', 'person_id', $id); }
	public function setPerson(Person $p)  { parent::setForeignKeyObject('Person', 'person_id', $p);  }

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
}