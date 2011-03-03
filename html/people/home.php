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
 * @param GET return_url
 */
if (!userIsAllowed('People')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

$template = new Template();
$searchForm = new Block('people/searchForm.inc');
if (isset($_GET['return_url'])) {
	$searchForm->return_url = $_GET['return_url'];
}
$template->blocks[] = $searchForm;



// Include the search form and results
$fields = array('name','email','address','phone');
foreach ($fields as $field) {
	if (isset($_GET[$field]) && trim($_GET[$field])) {
		$personList = new PersonList();
		$personList->search($_GET);

		$searchResults = new Block('people/searchResults.inc',array('personList'=>$personList));
		if (isset($_GET['return_url'])) {
			$searchResults->return_url = $_GET['return_url'];
		}
		$template->blocks[] = $searchResults;
		break;
	}
}

echo $template->render();
