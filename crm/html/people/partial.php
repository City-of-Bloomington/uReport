<?php
/**
 * Displays a single block
 *
 * The script is a mirror of /people/home.php
 * It responds to the same requests, but also lets you specify
 * a single block to to output.
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$block = new Block($_GET['partial']);

if (isset($_GET['return_url'])) {
	$block->return_url = $_GET['return_url'];
}

// Look for anything that the user searched for
$search = array();
$fields = array('firstname','lastname','email','organization');
foreach ($fields as $field) {
	if (isset($_GET[$field]) && $_GET[$field]) {
		$value = trim($_GET[$field]);
		if ($value) {
			$search[$field] = $value;
		}
	}
}

if (count($search)) {
	if (isset($_GET['setOfPeople'])) {
		switch ($_GET['setOfPeople']) {
			case 'staff':
				$search['username'] = array('$exists'=>true);
				break;
			case 'public':
				$search['username'] = array('$exists'=>false);
				break;
		}
	}
	$personList = new PersonList();
	$personList->search($search);
	$block->personList = $personList;
}

$template = new Template('partial','html');
$template->blocks[] = $block;
echo $template->render();