<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
if (!userIsAllowed('Categories')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

$categoryList = new CategoryList();
$categoryList->find();

$template = new Template('two-column');
$template->blocks[] = new Block('categories/categoryList.inc',array('categoryList'=>$categoryList));
echo $template->render();