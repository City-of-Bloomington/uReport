<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
if (!userIsAllowed('Constituents')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}


$template = new Template();

// Include the search form and results
$template->blocks[] = new Block('constituents/searchForm.inc');
$fields = array('firstname','lastname','email','address','phone');
foreach ($fields as $field) {
	if (isset($_GET[$field]) && trim($_GET[$field])) {
		$constituentList = new ConstituentList();
		$constituentList->search($_GET);

		$template->blocks[] = new Block('constituents/searchResults.inc',
										array('constituentList'=>$constituentList));
		break;
	}
}

echo $template->render();