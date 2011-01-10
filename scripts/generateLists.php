<?php
/**
 * Generates a Collection class for each the ActiveRecord objects
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
	// Output the class
	//--------------------------------------------------------------------------
$contents = "<?php
/**
 * A collection class for $className objects
 *
 * This class creates a zend_db select statement.
 * ZendDbResultIterator handles iterating and paginating those results.
 * As the results are iterated over, ZendDbResultIterator will pass each desired
 * row back to this class's loadResult() which will be responsible for hydrating
 * each $className object
 *
 * Beyond the basic \$fields handled, you will need to write your own handling
 * of whatever extra \$fields you need
 */
";
$contents.= COPYRIGHT;
$contents.="
class {$className}List extends ZendDbResultIterator
{
	/**
	 * Creates a basic select statement for the collection.
	 *
	 * Populates the collection if you pass in \$fields
	 * Setting itemsPerPage turns on pagination mode
	 * In pagination mode, this will only load the results for one page
	 *
	 * @param array \$fields
	 * @param int \$itemsPerPage Turns on Pagination
	 * @param int \$currentPage
	 */
	public function __construct(\$fields=null,\$itemsPerPage=null,\$currentPage=null)
	{
		parent::__construct(\$itemsPerPage,\$currentPage);
		if (is_array(\$fields)) {
			\$this->find(\$fields);
		}
	}

	/**
	 * Populates the collection
	 *
	 * @param array \$fields
	 * @param string|array \$order Multi-column sort should be given as an array
	 * @param int \$limit
	 * @param string|array \$groupBy Multi-column group by should be given as an array
	 */
	public function find(\$fields=null,\$order='$key',\$limit=null,\$groupBy=null)
	{
		\$this->select->from('$tableName');

		// Finding on fields from the $tableName table is handled here
		if (count(\$fields)) {
			foreach (\$fields as \$key=>\$value) {
				\$this->select->where(\"\$key=?\",\$value);
			}
		}

		// Finding on fields from other tables requires joining those tables.
		// You can handle fields from other tables by adding the joins here
		// If you add more joins you probably want to make sure that the
		// above foreach only handles fields from the $tableName table.

		\$this->select->order(\$order);
		if (\$limit) {
			\$this->select->limit(\$limit);
		}
		if (\$groupBy) {
			\$this->select->group(\$groupBy);
		}
		\$this->populateList();
	}

	/**
	 * Hydrates all the $className objects from a database result set
	 *
	 * This is a callback function, called from ZendDbResultIterator.  It is
	 * called once per row of the result.
	 *
	 * @param int \$key The index of the result row to load
	 * @return $className
	 */
	protected function loadResult(\$key)
	{
		return new $className(\$this->result[\$key]);
	}
}
";
	$dir = APPLICATION_HOME.'/scripts/stubs/classes';
	if (!is_dir($dir)) {
		mkdir($dir,0770,true);
	}
	file_put_contents("$dir/{$className}List.php",$contents);
	echo "$className\n";
}
