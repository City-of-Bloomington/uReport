<?php
/**
 * Generates ActiveRecord class files for each of the database tables
 *
 * @copyright 2006-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include '../configuration.inc';
$zend_db = Database::getConnection();

foreach ($zend_db->listTables() as $tableName) {
	$fields = array();
	$primary_keys = array();
	foreach ($zend_db->describeTable($tableName) as $row) {
		$type = preg_replace("/[^a-z]/","",strtolower($row['DATA_TYPE']));

		// Translate database datatypes into PHP datatypes
		if (preg_match('/int/',$type)) {
			$type = 'int';
		}
		if (preg_match('/enum/',$type) || preg_match('/varchar/',$type)) {
			$type = 'string';
		}

		$fields[] = array('field'=>$row['COLUMN_NAME'],'type'=>$type);

		if ($row['PRIMARY']) {
			$primary_keys[] = $row['COLUMN_NAME'];
		}
	}

	// Only generate code for tables that have a single-column primary key
	// Code for other tables will need to be created by hand
	if (count($primary_keys) != 1) {
		continue;
	}
	$key = $primary_keys[0];

	$tableName = strtolower($tableName);
	$className = Inflector::classify($tableName);
	//--------------------------------------------------------------------------
	// Constructor
	//--------------------------------------------------------------------------
	$constructor = "
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
	 * @param int|array \$$key
	 */
	public function __construct(\$$key=null)
	{
		if (\$$key) {
			if (is_array(\$$key)) {
				\$result = \$$key;
			}
			else {
				\$zend_db = Database::getConnection();
				\$sql = 'select * from $tableName where $key=?';
				\$result = \$zend_db->fetchRow(\$sql,array(\$$key));
			}

			if (\$result) {
				foreach (\$result as \$field=>\$value) {
					if (\$value) {
						\$this->\$field = \$value;
					}
				}
			}
			else {
				throw new Exception('$tableName/unknown$className');
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
		}
	}
	";

	//--------------------------------------------------------------------------
	// Properties
	//--------------------------------------------------------------------------
	$properties = '';
	$linkedProperties = array();
	foreach ($fields as $field) {
		$properties.= "\tprivate \$$field[field];\n";

		if (substr($field['field'],-3) == '_id') {
			$linkedProperties[] = $field['field'];
		}
	}

	if (count($linkedProperties)) {
		$properties.="\n\n";
		foreach ($linkedProperties as $property) {
			$field = substr($property,0,-3);
			$properties.= "\tprivate \$$field;\n";
		}
	}

	//--------------------------------------------------------------------------
	// Getters
	//--------------------------------------------------------------------------
	$getters = '';
	foreach ($fields as $field) {
		$fieldFunctionName = ucwords($field['field']);

		switch ($field['type'])
		{
			case 'date':
			case 'datetime':
			case 'timestamp':
				$getters.= "
	/**
	 * Returns the date/time in the desired format
	 *
	 * Format is specified using PHP's date() syntax
	 * http://www.php.net/manual/en/function.date.php
	 * If no format is given, the Date object is returned
	 *
	 * @param string \$format
	 * @return string|DateTime
	 */
	public function get$fieldFunctionName(\$format=null)
	{
		if (\$format && \$this->$field[field]) {
			return \$this->$field[field]->format(\$format);
		}
		else {
			return \$this->$field[field];
		}
	}
";
			break;

			default: $getters.= "
	/**
	 * @return $field[type]
	 */
	public function get$fieldFunctionName()
	{
		return \$this->$field[field];
	}
";
		}
	}

	foreach ($linkedProperties as $property) {
		$field = substr($property,0,-3);
		$fieldFunctionName = ucwords($field);
		$getters.= "
	/**
	 * @return $fieldFunctionName
	 */
	public function get$fieldFunctionName()
	{
		if (\$this->$property) {
			if (!\$this->$field) {
				\$this->$field = new $fieldFunctionName(\$this->$property);
			}
			return \$this->$field;
		}
		return null;
	}
";
	}


	//--------------------------------------------------------------------------
	// Setters
	//--------------------------------------------------------------------------
	$setters = '';
	foreach ($fields as $field) {
		if ($field['field'] != $key) {
			$fieldFunctionName = ucwords($field['field']);
			switch ($field['type']) {
				case 'int':
					if (in_array($field['field'],$linkedProperties)) {
						$property = substr($field['field'],0,-3);
						$object = ucfirst($property);
						$setters.= "
	/**
	 * @param $field[type] \$$field[type]
	 */
	public function set$fieldFunctionName(\$$field[type])
	{
		\$this->$property = new $object(\$int);
		\$this->$field[field] = \$$field[type];
	}
";
					}
					else {
						$setters.= "
	/**
	 * @param $field[type] \$$field[type]
	 */
	public function set$fieldFunctionName(\$$field[type])
	{
		\$this->$field[field] = preg_replace(\"/[^0-9]/\",\"\",\$$field[type]);
	}
";
					}
					break;

				case 'string':
					$setters.= "
	/**
	 * @param $field[type] \$$field[type]
	 */
	public function set$fieldFunctionName(\$$field[type])
	{
		\$this->$field[field] = trim(\$$field[type]);
	}
";
					break;

				case 'date':
				case 'datetime':
				case 'timestamp':
	$setters.= "
	/**
	 * Sets the date
	 *
	 * Date arrays should match arrays produced by getdate()
	 *
	 * Date string formats should be in something strtotime() understands
	 * http://www.php.net/manual/en/function.strtotime.php
	 *
	 * @param int|string|array \$$field[type]
	 */
	public function set$fieldFunctionName(\$$field[type])
	{
		if (\$$field[type]) {
			\$this->$field[field] = new Date(\$$field[type]);
		}
		else {
			\$this->$field[field] = null;
		}
	}
";
					break;

				case 'float':
					$setters.= "
	/**
	 * @param $field[type] \$$field[type]
	 */
	public function set$fieldFunctionName(\$$field[type])
	{
		\$this->$field[field] = preg_replace(\"/[^0-9.\-]/\",\"\",\$$field[type]);
	}
";
					break;

				case 'bool':
					$setters.= "
	/**
	 * @param boolean \$$field[type]
	 */
	public function set$fieldFunctionName(\$$field[type])
	{
		\$this->$field[field] = \$$field[type] ? true : false;
	}
";
					break;

				default:
					$setters.= "
	/**
	 * @param $field[type] \$$field[type]
	 */
	public function set$fieldFunctionName(\$$field[type])
	{
		\$this->$field[field] = \$$field[type];
	}
";
			}
		}
	}

	foreach ($linkedProperties as $field) {
		$property = substr($field,0,-3);
		$object = ucfirst($property);
		$setters.= "
	/**
	 * @param $object \$$property
	 */
	public function set$object(\$$property)
	{
		\$this->$field = \${$property}->getId();
		\$this->$property = \$$property;
	}
";
	}

	//--------------------------------------------------------------------------
	// Output the class
	//--------------------------------------------------------------------------
$contents = "<?php\n";
$contents.= COPYRIGHT;
$contents.= "
class $className
{
$properties

$constructor
	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception \$e
	 */
	public function validate()
	{
		// Check for required fields here.  Throw an exception if anything is missing.

	}

	/**
	 * Saves this record back to the database
	 */
	public function save()
	{
		\$this->validate();

		\$data = array();
";
			foreach ($fields as $field) {
				if ($field['field'] != $key) {
					$contents.="\t\t\$data['$field[field]'] = \$this->$field[field] ? \$this->$field[field] : null;\n";
				}
			}
$contents.= "
		if (\$this->$key) {
			\$this->update(\$data);
		}
		else {
			\$this->insert(\$data);
		}
	}

	private function update(\$data)
	{
		\$zend_db = Database::getConnection();
		\$zend_db->update('$tableName',\$data,\"$key='{\$this->$key}'\");
	}

	private function insert(\$data)
	{
		\$zend_db = Database::getConnection();
		\$zend_db->insert('$tableName',\$data);
		\$this->$key = \$zend_db->lastInsertId('$tableName','$key');
	}

	//----------------------------------------------------------------
	// Generic Getters
	//----------------------------------------------------------------
$getters
	//----------------------------------------------------------------
	// Generic Setters
	//----------------------------------------------------------------
$setters

	//----------------------------------------------------------------
	// Custom Functions
	// We recommend adding all your custom code down here at the bottom
	//----------------------------------------------------------------
}
";
	$dir = APPLICATION_HOME.'/scripts/stubs/classes';
	if (!is_dir($dir)) {
		mkdir($dir,0770,true);
	}
	file_put_contents("$dir/$className.php",$contents);
	echo "$className\n";
}
