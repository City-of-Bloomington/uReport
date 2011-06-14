<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST category_id
 * @param REQUEST index
 */
if (!userIsAllowed('Categories')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

try {
	$category = new Category($_REQUEST['category_id']);
}
catch (Exception $e) {
	$_SESSION['errorMessages'][] = $e;
	header('Location: '.BASE_URL.'/categories');
	exit();
}


if (isset($_REQUEST['index'])) {
	$category->removeProblem($_REQUEST['index']);
}
try {
	$category->save();
}
catch (Exception $e) {
	$_SESSION['errorMessages'][] = $e;
}

header('Location: '.BASE_URL.'/categories/problems.php?category_id='.$category->getId());
