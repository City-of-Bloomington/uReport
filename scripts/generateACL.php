<?php
/**
 * Generates an access control file with a resource for each Class we will generate
 *
 * @copyright 2010 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include '../configuration.inc';
$zend_db = Database::getConnection();
$resources = array();
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
	$resources[] = Inflector::pluralize($className);
}

$contents = "<?php\n";
$contents.= COPYRIGHT."\n";
$contents.= "
\$ZEND_ACL = new Zend_Acl();
/**
 * Load the roles from the database
 */
\$roles = new RoleList();
\$roles->find();
foreach (\$roles as \$role) {
	\$ZEND_ACL = \$ZEND_ACL->addRole(new Zend_Acl_Role(\$role->getName()));
}

/**
 * Declare all the resources
 */
";
foreach ($resources as $acl_resource) {
	$contents.= "\$ZEND_ACL->add(new Zend_Acl_Resource('$acl_resource'));\n";
}
$contents.= "
/**
 * Assign permissions to the resources
 */
// Administrator is allowed access to everything
\$ZEND_ACL->allow('Administrator');
";


$dir = APPLICATION_HOME."/scripts/stubs";
if (!is_dir($dir)) {
	mkdir($dir,0770,true);
}
file_put_contents("$dir/access_control.inc",$contents);