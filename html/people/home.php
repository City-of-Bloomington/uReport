<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
if (!userIsAllowed('People')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

$template = new Template();

// Include the search form and results
$template->blocks[] = new Block('people/searchForm.inc');
$fields = array('name','email','address','phone');
foreach ($fields as $field) {
	if (isset($_GET[$field]) && trim($_GET[$field])) {
		$personList = new PersonList();
		$personList->search($_GET);

		$template->blocks[] = new Block('people/searchResults.inc',
										array('personList'=>$personList));
		break;
	}
}

echo $template->render();
