<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
if (!userIsAllowed('Cases')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

$template = new Template('search');
$template->blocks['search-form'][] = new Block('cases/searchForm.inc');
// Map the form fields to the Case search fields
$fields = array(
	'enteredByPerson'=>'enteredByPerson._id',
	'assignedPerson'=>'assignedPerson._id',
	'department'=>'assignedPerson.department._id',
	'city'=>'city',
	'state'=>'state',
	'zip'=>'zip',
	'type'=>'issues.type',
	'category'=>'issues.category._id',
	'contactMethod'=>'issues.contactMethod',
	'status'=>'status',
	'action'=>'history.action',
	'actionPerson'=>'history.actionPerson._id'
);
if (count(array_intersect(array_keys($fields),array_keys($_GET)))) {
	$search = array();
	foreach ($fields as $field=>$key) {
		if (isset($_GET[$field])) {
			$value = trim($_GET[$field]);
			if ($value) {
				$search[$key] = $value;
			}
		}
	}

	if (count($search)) {
		$caseList = new CaseList($search);

		$page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
		$paginator = $caseList->getPaginator(50,$page);

		$template->blocks['search-results'][] = new Block(
			'cases/searchResults.inc',
			array(
				'caseList'=>$paginator,
				'title'=>'Search Results',
				'fields'=>isset($_GET['fields']) ? $_GET['fields'] : null
			)
		);
		$template->blocks['search-results'][] = new Block(
			'pageNavigation.inc',array('paginator'=>$paginator)
		);

	}
}

echo $template->render();
