<?php
/**
 * Handles adding or editing CategoryNotes
 *
 * Either a note_id or an category_id is required
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST index
 * @param REQUEST category_id
 */
// Make sure they're allowed to be here
if (!userIsAllowed('Categories')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

// Load the category and problem
try {
	$category = new Category($_REQUEST['category_id']);
	
	if (isset($_REQUEST['index']) && preg_match('/[0-9]+/',$_REQUEST['index'])) {
		$index = $_REQUEST['index'];
		$problems = $category->getProblems();
		$problem = $problems[$index];
	}
	else {
		$index = null;
		$problem = '';
	}
}
catch (Exception $e) {
	$_SESSION['errorMessages'][] = $e;
	header('Location: '.BASE_URL.'/categories');
	exit();
}


// Handle POST data
if (isset($_POST['problem'])) {
	$category->updateProblems($_POST['problem'],$index);
	try {
		$category->save();
		header('Location: '.BASE_URL.'/categories/problems.php?category_id='.$category->getId());
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

// Display the form
$template = new Template('two-column');
$template->blocks[] = new Block(
	'categories/updateProblemForm.inc',
	array('category'=>$category,'problem'=>$problem,'index'=>$index)
);
echo $template->render();

