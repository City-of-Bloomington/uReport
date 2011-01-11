<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
if (!userIsAllowed('NeighborhoodAssociations')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

$neighborhoodAssociationList = new NeighborhoodAssociationList();
$neighborhoodAssociationList->find();

$template = new Template('two-column');
$template->blocks[] = new Block('neighborhoodAssociations/neighborhoodAssociationList.inc',
								array('neighborhoodAssociationList'=>$neighborhoodAssociationList));
echo $template->render();