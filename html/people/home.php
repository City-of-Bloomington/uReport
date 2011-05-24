<?php
/**
 * Find and choose people
 *
 * The user can come here from somewhere they need a person
 * Choosing a person should send them back where they came from,
 * with the chosen person appended to the url
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET personQuery
 * @param GET return_url
 */
if (!userIsAllowed('People')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

// Look for anything that the user searched for
$search = array();
$fields = array('firstname','lastname','email','organization');
foreach ($fields as $field) {
	if (isset($_GET[$field]) && $_GET[$field]) {
		$value = trim($_GET[$field]);
		if ($value) {
			$search[$field] = $value;
		}
	}
}
$personQuery = isset($_GET['personQuery']) ? trim($_GET['personQuery']) : '';
if ($personQuery) {
	$search = array('query'=>$personQuery);
}

// Display the search form and any results
$template = new Template();
$searchForm = new Block('people/searchForm.inc');
if (isset($_GET['return_url'])) {
	$searchForm->return_url = $_GET['return_url'];
}
$template->blocks[] = $searchForm;

if (count($search)) {
	$personList = new PersonList();
	$personList->search($search);
	$searchResults = new Block('people/searchResults.inc',array('personList'=>$personList));
	if (isset($_GET['return_url'])) {
		$searchResults->return_url = $_GET['return_url'];
	}
	$template->blocks[] = $searchResults;
}
echo $template->render();
