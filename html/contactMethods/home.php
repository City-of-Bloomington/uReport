<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
if (!userIsAllowed('ContactMethods')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}
$contactMethodList = new ContactMethodList();
$contactMethodList->find();

$template = new Template('two-column');
$template->blocks[] = new Block('contactMethods/contactMethodList.inc',array('contactMethodList'=>$contactMethodList));
echo $template->render();