<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
if (!userIsAllowed('Resolutions')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

$resolutionList = new ResolutionList();
$resolutionList->find();

$template = new Template('two-column');
$template->blocks[] = new Block('resolutions/resolutionList.inc',array('resolutionList'=>$resolutionList));
echo $template->render();