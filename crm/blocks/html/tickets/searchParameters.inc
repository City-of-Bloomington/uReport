<?php
/**
 * Displays the fields the user searched on
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */

// Go through all the possible search fields and translate the raw
// search values into the human readable names
$params = array();
$displayableFields = TicketList::getDisplayableFields();
$searchableFields = TicketList::getSearchableFields();
foreach (TicketList::getSearchableFields() as $key=>$value) {
	if (!empty($_GET[$key])) {
		if (isset($displayableFields[$key])) {
			$name = $displayableFields[$key]['displayName'];
		}
		else {
			$name = ucfirst($key);
		}

		$p = array('key'=>$key,'displayName'=>$name);

		if (is_array($_GET[$key])) {
			foreach ($_GET[$key] as $value) {
				$v = array('key'=>$value);
				switch ($key) {
					case 'enteredByPerson':
					case 'assignedPerson':
					case 'referredPerson':
						$person = new Person($value);
						$v['displayName'] = $person->getFullname();
						break;

					case 'department':
						$department = new Department($value);
						$v['displayName'] = $department->getName();
						break;

					case 'category':
						$category = new Category($value);
						$v['displayName'] = $category->getName();
						break;

					default:
						$v['displayName'] = $value;
				}
				$p['values'][] = $v;
			}
		}
		else {
			$p['value'] = array('key'=>$_GET[$key],'displayName'=>$_GET[$key]);
		}
		$params[] = $p;
	}
}

if ($params) {
	echo "<div class=\"searchParameters\"><h1>Current Search</h1><p>Your current search parameters. Click on one of the labels to delete that parameter from your search.</p><span> ";
	foreach ($params as $p) {
		$key = $p['key'];
		$displayName = $p['displayName'];
		if (isset($p['value'])) {
			$url = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
			unset($url->parameters[$key]);
			echo "<a class=\"delete button\" href=\"$url\">".View::escape("$displayName: {$p['value']['displayName']}")."</a>";

		}
		else {
			foreach ($p['values'] as $i=>$v) {
				$url = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
				unset($url->parameters[$key][$i]);
				echo "<a class=\"delete button\" href=\"$url\">".View::escape("$displayName: $v[displayName]")."</a>";
			}
		}
	}
	echo "</span></div>";
}