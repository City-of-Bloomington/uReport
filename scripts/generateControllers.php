<?php
/**
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
	$variableName = Inflector::singularize($tableName);
	$acl_resource = ucfirst($tableName);

/**
 * Generate home.php
 */
$PHP = "
\${$variableName}List = new {$className}List();
\${$variableName}List->find();

\$template = new Template();
\$template->blocks[] = new Block('{$variableName}s/{$variableName}List.inc',array('{$variableName}List'=>\${$variableName}List));
echo \$template->render();";

$contents = "<?php\n";
$contents.= COPYRIGHT."\n";
$contents.= $PHP;

	$dir = APPLICATION_HOME."/scripts/stubs/html/$tableName";
	if (!is_dir($dir)) {
		mkdir($dir,0770,true);
	}
	file_put_contents("$dir/home.php",$contents);

/**
 * Generate the Update controller
 */
$fieldArray = array();
foreach ($fields as $row) {
	$fieldArray[] = "'$row[field]'";
}
$fieldArray = implode(',',$fieldArray);

$PHP = "
if (!userIsAllowed('$acl_resource')) {
	\$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL.'/$tableName');
	exit();
}

// Load the \${$variableName} for editing
if (isset(\$_REQUEST['$key']) && \$_REQUEST['$key']) {
	try {
		\${$variableName} = new $className(\$_REQUEST['$key']);
	}
	catch (Exception \$e) {
		\$_SESSION['errorMessages'][] = \$e;
		header('Location: '.BASE_URL.'/$tableName');
		exit();
	}
}
else {
	\${$variableName} = new $className();
}


if (isset(\$_POST['$key'])) {
	\$fields = array($fieldArray);
	foreach (\$fields as \$field) {
		if (isset(\$_POST[\$field])) {
			\$set = 'set'.ucfirst(\$field);
			\${$variableName}->\$set(\$_POST[\$field]);
		}
	}

	try {
		\${$variableName}->save();
		header('Location: '.BASE_URL.'/$tableName');
		exit();
	}
	catch (Exception \$e) {
		\$_SESSION['errorMessages'][] = \$e;
	}
}

\$template = new Template();
\$template->blocks[] = new Block('{$variableName}s/update{$className}Form.inc',array('{$variableName}'=>\${$variableName}));
echo \$template->render();";
$contents = "<?php\n";
$contents.= COPYRIGHT."\n";
$contents.= $PHP;
	file_put_contents("$dir/update{$className}.php",$contents);
	echo "$className\n";
}
