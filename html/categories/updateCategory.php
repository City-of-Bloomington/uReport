<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
if (!userIsAllowed('Categories')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

// Load the $category for editing
if (isset($_REQUEST['category_id']) && $_REQUEST['category_id']) {
	try {
		$category = new Category($_REQUEST['category_id']);
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


if (isset($_POST['name'])) {
	$category->setName($_POST['name']);

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