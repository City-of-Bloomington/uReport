<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Blossom\Classes;
use Zend\Db\Sql\Sql;

abstract class ActiveRecord
{
	protected $tablename;
	protected $data = array();

	const MYSQL_DATE_FORMAT = 'Y-m-d H:i:s';

	abstract public function validate();

	/**
	 * Callback from TableGateway
	 */
	public function exchangeArray($data)
	{
		$this->data = $data;
	}

	/**
	 * Writes the database back to the database
	 */
	protected function save()
	{
		$this->validate();
		$zend_db = Database::getConnection();
		$sql = new Sql($zend_db, $this->tablename);
		if ($this->getId()) {
			$update = $sql->update()
				->set($this->data)
				->where(array('id'=>$this->getId()));
			$sql->prepareStatementForSqlObject($update)->execute();
		}
		else {
			$insert = $sql->insert()->values($this->data);
			$sql->prepareStatementForSqlObject($insert)->execute();
			$this->data['id'] = $zend_db->getDriver()->getLastGeneratedValue();
		}
	}

	/**
	 * Removes this record from the database
	 */
	protected function delete()
	{
		if ($this->getId()) {
			$sql = new Sql(Database::getConnection(), $this->tablename);
			$delete = $sql->delete()->where(['id'=>$this->getId()]);
			$sql->prepareStatementForSqlObject($delete)->execute();
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
	 * If no format is given, the database's raw data is returned
	 *
	 * @param string $field
	 * @param string $format
	 * @param DateTimeZone $timezone
	 * @return string
	 */
	protected function getDateData($dateField, $format=null, \DateTimeZone $timezone=null)
	{
		if (isset($this->data[$dateField])) {
			if ($format) {
				$date = new \DateTime($this->data[$dateField]);
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
	 * Dates should be in DATE_FORMAT, set in configuration.inc
	 * If we cannot parse the string using DATE_FORMAT, we will
	 * fall back to trying something strtotime() understands
	 * http://www.php.net/manual/en/function.strtotime.php
	 *
	 * @param string $dateField
	 * @param string $date
	 */
	protected function setDateData($dateField, $date)
	{
		$date = trim($date);
		if ($date) {
			$d = \DateTime::createFromFormat(DATE_FORMAT, $date);
			if (!$d) {
				try {
					$d = new \DateTime($date);
				}
				catch (\Exception $e) {
					throw new \Exception('unknownDateFormat');
				}
			}
			$this->data[$dateField] = $d->format(self::MYSQL_DATE_FORMAT);
		}
		else {
			$this->data[$dateField] = null;
		}
	}

	/**
	 * Loads and returns an object for a foreign key _id field
	 *
	 * Will cache the object in a protected variable to avoid multiple database
	 * lookups. Make sure to declare a protected variable matching the class
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
	 * @param string $class Fully namespaced classname
	 * @param string $field Name of field to set
	 * @param string $id The value to set
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
	 * @param string $class Fully namespaced classname
	 * @param string $field Name of field to set
	 * @param Object $object Value to set
	 */
	protected function setForeignKeyObject($class, $field, $object)
	{
		if ($object instanceof $class) {
			$var = preg_replace('/_id$/', '', $field);
			$this->data[$field] = $object->getId();
			$this->$var = $object;
		}
		else {
			throw new \Exception('Object does not match the given class');
		}
	}

	/**
	 * Returns whether the value can be an ID for a record
	 *
	 * return @bool
	 */
	public static function isId($id)
	{
		return ((is_int($id) && $id>0) || (is_string($id) && ctype_digit($id)));
	}
}
