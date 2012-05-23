<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
abstract class ActiveRecord
{
	protected $tablename;
	protected $data = array();

	const MYSQL_DATE_FORMAT = 'Y-m-d H:i:s';

	abstract public function validate();

	/**
	 * Writes the database back to the database
	 */
	protected function save()
	{
		$this->validate();
		$zend_db = Database::getConnection();
		if ($this->getId()) {
			$zend_db->update($this->tablename, $this->data, "id={$this->getId()}");
		}
		else {
			$zend_db->insert($this->tablename, $this->data);
			$this->data['id'] = $zend_db->lastInsertId($this->tablename, 'id');
		}
	}

	/**
	 * Removes this record from the database
	 */
	protected function delete()
	{
		if ($this->getId()) {
			$zend_db = Database::getConnection();
			$zend_db->delete($this->tablename, 'id='.$this->getId());
		}
	}

	/**
	 * Returns any field stored in $data
	 *
	 * @param string $fieldname
	 */
	protected function get($fieldname)
	{
		if (isset($this->data[$fieldname])) {
			return $this->data[$fieldname];
		}
	}

	/**
	 * @param string $fieldname
	 * @param string $value
	 */
	protected function set($fieldname, $value)
	{
		$value = trim($value);
		$this->data[$fieldname] = $value ? $value : null;
	}

	/**
	 * Returns the date/time in the desired format
	 *
	 * Format is specified using PHP's date() syntax
	 * http://www.php.net/manual/en/function.date.php
	 * If no format is given, the MongoDate object is returned
	 *
	 * @param string $field
	 * @param string $format
	 * @param DateTimeZone $timezone
	 * @return string
	 */
	protected function getDateData($dateField, $format=null, DateTimeZone $timezone=null)
	{
		if (isset($this->data[$dateField])) {
			if ($format) {
				$date = new DateTime($this->data[$dateField]);
				if ($timezone) { $date->setTimezone($timezone); }
				return $date->format($format);
			}
			else {
				return $this->data[$dateField];
			}
		}
	}

	/**
	 * Sets a date
	 *
	 * Dates should be in something strtotime() understands
	 * http://www.php.net/manual/en/function.strtotime.php
	 *
	 * @param string $dateField
	 * @param string $date
	 */
	protected function setDateData($dateField, $date)
	{
		$date = trim($date);
		if ($date) {
			$date = new DateTime($date);
			$this->data[$dateField] = $date->format(self::MYSQL_DATE_FORMAT);
		}
		else {
			$this->data[$dateField] = null;
		}
	}

	/**
	 * Loads and returns an object for a foreign key _id field
	 *
	 * Will cache the object in a private variable to avoid multiple database lookups.
	 * Make sure to declare a private variable matching the class
	 *
	 * @param string $class
	 * @param string $field
	 */
	protected function getForeignKeyObject($class, $field)
	{
		$var = preg_replace('/_id$/', '', $field);
		if (!$this->$var && isset($this->data[$field])) {
			$this->$var = new $class($this->data[$field]);
		}
		return $this->$var;
	}

	/**
	 * Verifies and saves the ID for a foreign key field
	 *
	 * Loads the object record for the foreign key and caches
	 * the object in a private variable
	 *
	 * @param string $class
	 * @param string $field
	 * @param string $id
	 */
	protected function setForeignKeyField($class, $field, $id)
	{
		$id = trim($id);
		$var = preg_replace('/_id$/', '', $field);
		if ($id) {
			$this->$var = new $class($id);
			$this->data[$field] = $this->$var->getId();
		}
		else {
			$this->$field = null;
			$this->data[$field] = null;
		}
	}

	/**
	 * Verifies and saves the ID for a foreign key object
	 *
	 * Caches the object in a private variable and sets
	 * the ID value in the data
	 *
	 * @param string $class
	 * @param string $field
	 * @param Object $object
	 */
	protected function setForeignKeyObject($class, $field, $object)
	{
		if ($object instanceof $class) {
			$var = preg_replace('/_id$/', '', $field);
			$this->data[$field] = $object->getId();
			$this->$var = $object;
		}
	}

	/**
	 * Returns whether the value can be an ID for a record
	 *
	 * return @bool
	 */
	public static function isId($id)
	{
		return (is_int($id) || (is_string($id) && ctype_digit($id)));
	}
}