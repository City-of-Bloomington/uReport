<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET return_url
 */
$return_url = new URL($_GET['return_url']);

$categoryList = new CategoryList();
$categoryList->find();

$template = new Template();
$template->blocks[] = new Block(
	'categories/categoryChoices.inc',
	array('categoryList'=>$categoryList,'return_url'=>$return_url)
);
echo $template->render();
