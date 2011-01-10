<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
if (!userIsAllowed('Users')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

$personList = new PersonList();
$personList->find();

$template = new Template();
$template->title = 'People';
$template->blocks[] = new Block('people/personList.inc',array('personList'=>$personList));
echo $template->render();
