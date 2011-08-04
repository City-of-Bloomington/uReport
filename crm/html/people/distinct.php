<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET field
 * @param GET query
 */
if (!userIsAllowed('People')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}
$results = Person::getDistinct($_GET['field'],$_GET['query']);

$template = (isset($_GET['format'])) ? new Template('default',$_GET['format']) : new Template();
$template->blocks[] = new Block('people/distinctFieldValues.inc',array('results'=>$results));
echo $template->render();