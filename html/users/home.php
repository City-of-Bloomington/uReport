<?php
/**
 * @copyright 2006-2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
if (!userIsAllowed('Users')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

$search = array('username'=>array('$exists'=>true));
if (isset($_GET['department_id']) && $_GET['department_id']) {
	$search['department._id'] = new MongoId($_GET['department_id']);
}
$people = new PersonList($search);



if (isset($_GET['format'])) {
	$template = new Template('default',$_GET['format']);
}
else {
	$template = new Template('two-column');
	$template->title = 'User accounts';
	$template->blocks[] = new Block('users/findForm.inc');
}

$template->blocks[] = new Block('users/userList.inc',array('userList'=>$people));

echo $template->render();
