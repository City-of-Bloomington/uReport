<?php
/**
 * @copyright 2008-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include '../configuration.inc';
$dir = APPLICATION_HOME.'/scripts/stubs/tests';
if (!is_dir($dir)) {
	mkdir($dir,0770,true);
}

$dir = APPLICATION_HOME.'/scripts/stubs/tests/DatabaseTests';
if (!is_dir($dir)) {
	mkdir($dir,0770,true);
}

$dir = APPLICATION_HOME.'/scripts/stubs/tests/UnitTests';
if (!is_dir($dir)) {
	mkdir($dir,0770,true);
}

$dir = APPLICATION_HOME.'/scripts/stubs/tests';


$zend_db = Database::getConnection();
$classes = array();
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
	$classes[] = $className;

	$variable = strtolower($className);

//------------------------------------------------------------------------------
// Generate the Unit Tests
//------------------------------------------------------------------------------
$contents = "<?php
require_once 'PHPUnit/Framework.php';

class {$className}UnitTest extends PHPUnit_Framework_TestCase
{
	public function testValidate()
	{
		\${$variable} = new {$className}();
		try {
			\${$variable}->validate();
			\$this->fail('Missing name failed to throw exception');
		}
		catch (Exception \$e) {

		}

		\${$variable}->setName('Test {$className}');
		\${$variable}->validate();
	}
}
";
file_put_contents("$dir/UnitTests/{$className}UnitTest.php",$contents);

//------------------------------------------------------------------------------
// Generate the Database Tests
//------------------------------------------------------------------------------
$contents = "<?php
require_once 'PHPUnit/Framework.php';

class {$className}DbTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		\$dir = dirname(__FILE__);
		exec('/usr/local/mysql/bin/mysql -u '.DB_USER.' -p'.DB_PASS.' '.DB_NAME.\" < \$dir/../testData.sql\");
	}

    public function testSaveLoad()
    {
		\${$variable} = new {$className}();
		\${$variable}->setName('Test {$className}');
    	try {
			\${$variable}->save();
			\$id = \${$variable}->getId();
			\$this->assertGreaterThan(0,\$id);
		}
		catch (Exception \$e) {
			\$this->fail(\$e->getMessage());
		}

		\${$variable} = new {$className}(\$id);
		\$this->assertEquals(\${$variable}->getName(),'Test {$className}');

		\${$variable}->setName('Test');
		\${$variable}->save();

		\${$variable} = new {$className}(\$id);
		\$this->assertEquals(\${$variable}->getName(),'Test');
    }
}
";
file_put_contents("$dir/DatabaseTests/{$className}DbTest.php",$contents);

//------------------------------------------------------------------------------
// Generate the Database List Tests
//------------------------------------------------------------------------------
$contents = "<?php
require_once 'PHPUnit/Framework.php';

class {$className}ListDbTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		\$dir = dirname(__FILE__);
		exec('/usr/local/mysql/bin/mysql -u '.DB_USER.' -p'.DB_PASS.' '.DB_NAME.\" < \$dir/../testData.sql\");
	}

	/**
	 * Makes sure find returns all $tableName ordered correctly by default
	 */
	public function testFindOrderedByName()
	{
		\$PDO = Database::getConnection();
		\$query = \$PDO->query('select id from $tableName order by id');
		\$result = \$query->fetchAll();

		\$list = new {$className}List();
		\$list->find();
		\$this->assertEquals(\$list->getSort(),'id');

		foreach (\$list as \$i=>\${$variable}) {
			\$this->assertEquals(\${$variable}->getId(),\$result[\$i]['id']);
		}
    }
}
";
file_put_contents("$dir/DatabaseTests/{$className}ListDbTest.php",$contents);

echo "$className\n";
}

//------------------------------------------------------------------------------
// Generate the All Tests Suite
//------------------------------------------------------------------------------
$contents = "<?php
require_once 'PHPUnit/Framework.php';

require_once 'UnitTests.php';
require_once 'DatabaseTests.php';

class AllTests extends PHPUnit_Framework_TestSuite
{
	public static function suite()
	{
		\$suite = new AllTests('".APPLICATION_NAME."');
		\$suite->addTest(UnitTests::suite());
		\$suite->addTest(DatabaseTests::suite());
		return \$suite;
	}
}
";
file_put_contents("$dir/AllTests.php",$contents);

//------------------------------------------------------------------------------
// Generate the All Tests Suite
//------------------------------------------------------------------------------
$contents = "<?php\nrequire_once 'PHPUnit/Framework.php';\n\n";
foreach ($classes as $className) {
	$contents.= "require_once 'DatabaseTests/{$className}DbTest.php';\n";
	$contents.= "require_once 'DatabaseTests/{$className}ListDbTest.php';\n";
}
$contents.= "
class DatabaseTests extends PHPUnit_Framework_TestSuite
{
	protected function setUp()
	{
		\$dir = dirname(__FILE__);
		exec('/usr/local/mysql/bin/mysql -u '.DB_USER.' -p'.DB_PASS.' '.DB_NAME.\" < \$dir/testData.sql\");
	}

	protected function tearDown()
	{
		\$dir = dirname(__FILE__);
		exec('/usr/local/mysql/bin/mysql -u '.DB_USER.' -p'.DB_PASS.' '.DB_NAME.\" < \$dir/testData.sql\");
	}

	public static function suite()
	{
		\$suite = new DatabaseTests('".APPLICATION_NAME." Classes');

";
foreach ($classes as $className) {
	$contents.= "\t\t\$suite->addTestSuite('{$className}DbTest');\n";
	$contents.= "\t\t\$suite->addTestSuite('{$className}ListDbTest');\n";
}
$contents.= "
		return \$suite;
	}
}
";
file_put_contents("$dir/DatabaseTests.php",$contents);

//------------------------------------------------------------------------------
// Generate the Unit Tests Suite
//------------------------------------------------------------------------------
$contents = "<?php\nrequire_once 'PHPUnit/Framework.php';\n\n";
foreach ($classes as $className) {
	$contents.= "require_once 'UnitTests/{$className}UnitTest.php';\n";
}
$contents.= "
class UnitTests extends PHPUnit_Framework_TestSuite
{
	public static function suite()
	{
		\$suite = new UnitTests('".APPLICATION_NAME." Classes');

";
foreach ($classes as $className) {
	$contents.= "\t\t\$suite->addTestSuite('{$className}UnitTest');\n";
}
$contents.= "
		return \$suite;
	}
}
";
file_put_contents("$dir/UnitTests.php",$contents);
