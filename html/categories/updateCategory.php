<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */

if (!userIsAllowed('Categories')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

// Load the $category for editing
if (isset($_REQUEST['id']) && $_REQUEST['id']) {
	try {
		$category = new Category($_REQUEST['id']);
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
		header('Location: '.BASE_URL.'/categories');
		exit();
	}
}
else {
	$category = new Category();
}


if (isset($_POST['id'])) {
	$fields = array('name','department_id');
	foreach ($fields as $field) {
		if (isset($_POST[$field])) {
			$set = 'set'.ucfirst($field);
			$category->$set($_POST[$field]);
		}
	}

	try {
		$category->save();
		header('Location: '.BASE_URL.'/categories');
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template('two-column');
$template->blocks[] = new Block('categories/updateCategoryForm.inc',array('category'=>$category));
echo $template->render();