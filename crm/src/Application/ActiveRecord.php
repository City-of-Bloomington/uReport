<?php
/**
 * @copyright 2011-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application;

abstract class ActiveRecord
{
    public const TABLENAME             = '';
    public const MYSQL_DATE_FORMAT     = 'Y-m-d';
    public const MYSQL_TIME_FORMAT     = 'H:i:s';
    public const MYSQL_DATETIME_FORMAT = 'Y-m-d H:i:s';

    abstract public function validate();

    public $data = [];
    public function exchangeArray(array $data)
    {
        $this->data = $data;
    }

    public function getId(): ?int { return $this->data['id'] ?? null; }

    protected function save()
    {
        $this->validate();
        $pdo   = Database::getConnection();
        $table = static::TABLENAME;

        $c = [];
        foreach ($this->data as $k=>$v) {
            if ($k != 'id') { $c[] = "$k=:$k"; }
        }
        $set = 'set '.implode(',', $c);

        if ($this->getId()) {
            $update = $pdo->prepare("update $table $set where id=:id");
            $update->execute($this->data);
        }
        else {
            unset($this->data['id']);

            $insert = $pdo->prepare("insert $table $set");
            $insert->execute($this->data);
            $this->data['id'] = $pdo->lastInsertId();
        }
    }

    /**
     * Removes this record from the database
     */
    protected function delete()
    {
        if ($this->getId()) {
            $pdo    = Database::getConnection();
            $table  = static::TABLENAME;
            $delete = $pdo->prepare("delete from $table where id=:id");
            $delete->execute(['id'=>$this->getId()]);
        }
    }

    protected function get(string $fieldname)
    {
        if ( isset($this->data[$fieldname])) {
            return $this->data[$fieldname];
        }
    }

    protected function set(string $fieldname, ?string $value=null)
    {
        if ($value) {
            $value = trim($value);
        }
        $this->data[$fieldname] = $value;
    }

    /**
     * Returns the date/time in the desired format
     *
     * Format is specified using PHP's date() syntax
     * http://www.php.net/manual/en/function.date.php
     * If no format is given, the database's raw data is returned
     */
    protected function getDateData(string $dateField, ?string $format=null, ?\DateTimeZone $timezone=null): ?string
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
        return null;
    }

    /**
     * Sets a date
     *
     * Dates should be in DATETIME_FORMAT, set in configuration.inc
     * If we cannot parse the string using DATETIME_FORMAT, we will
     * fall back to trying something strtotime() understands
     * http://www.php.net/manual/en/function.strtotime.php
     */
    protected function setDateData(string $dateField, string $date, string $format=DATETIME_FORMAT, string $databaseFormat=self::MYSQL_DATETIME_FORMAT)
    {
        if ($date) {
            $date = trim($date);
            try {
                $d = self::parseDate($date, $format);
                $this->data[$dateField] = $d->format($databaseFormat);
            }
            catch (\Exception $e) {
                $class = strtolower((new \ReflectionClass($this))->getShortName());
                throw new \Exception("$class/$dateField/invalidDate");
            }
        }
        else {
            $this->data[$dateField] = null;
        }
    }

    /**
     * Return a DateTime object for a date string
     *
     * Dates should be in $format.
     * If we cannot parse the string using $format, we will
     * fall back to trying something strtotime() understands
     * http://www.php.net/manual/en/function.strtotime.php
     */
    public static function parseDate(string $date, string $format=DATETIME_FORMAT): \DateTime
    {
        $d = \DateTime::createFromFormat($format, $date);
        if (!$d) {
            $d = new \DateTime($date);
        }
        return $d;
    }

    /**
     * Loads and returns an object for a foreign key _id field
     *
     * Will cache the object in a protected variable to avoid multiple database
     * lookups. Make sure to declare a protected variable matching the class
     */
    protected function getForeignKeyObject(string $class, string $field)
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
     */
    protected function setForeignKeyField(string $class, string $field, ?string $id=null)
    {
        $var = preg_replace('/_id$/', '', $field);

        if ($id) {
            $id  = trim($id);
            $this->$var = new $class($id);
            $this->data[$field] = $this->$var->getId();
        }
        else {
            $this->$var         = null;
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
    protected function setForeignKeyObject(string $class, string $field, $object)
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
     */
    public static function isId($id): bool
    {
        return ((is_int($id) && $id>0) || (is_string($id) && ctype_digit($id)));
    }
}
