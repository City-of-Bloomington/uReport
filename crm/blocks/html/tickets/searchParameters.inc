<?php
/**
 * Displays the fields the user searched on
 *
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
function lookupHumanReadableValue($key, $value)
{
	switch ($key) {
		case 'enteredByPerson':
		case 'assignedPerson':
		case 'referredPerson':
			$person = new Person($value);
			return $person->getFullname();
			break;

		case 'department':
			$department = new Department($value);
			return $department->getName();
			break;

		case 'category':
			$category = new Category($value);
			return $category->getName();
			break;

		case 'client_id':
			$client = new Client($value);
			return $client->getName();
			break;

		default:
			return $value;
	}
}
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
				$p['values'][] = array(
					'key'=>$value,
					'displayName'=>lookupHumanReadableValue($key,$value)
				);
			}
		}
		else {
			$p['value'] = array(
				'key'=>$_GET[$key],
				'displayName'=>lookupHumanReadableValue($key,$_GET[$key])
			);
		}
		$params[] = $p;
	}
}

if ($params) {
	echo "
	<div class=\"searchParameters\">
		<h2>Current Search</h2>
		<p>Your current search parameters.
			Click on one of the labels to delete that parameter from your search.
		</p>
		<span>
	";
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
	echo "
		</span>
	</div>
	";
}