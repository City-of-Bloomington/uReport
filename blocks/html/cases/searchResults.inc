<?php
/**
 * Displays a list of Cases
 *
 * This page only displays Case and Issue fields that are asked for
 * The fields desired need to be the indexes of an associative array
 * The values for each field just needs to be truthy
 *
 * $exampleFields = array('case-id'=>1,'case-status'=>1,'issue-issueType'=>1);
 *
 * For the full list of possible fields for use here,
 * See: CaseList::getDisplayableFields()
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param CaseList $this->caseList
 * @param array $this->fields The case fields to display.
 */
// Default fields to be used
if (!$this->fields) {
	$this->fields = array(
		'id'=>1,'status'=>1,
		'location'=>1,'enteredDate'=>1,'enteredByPerson'=>1
	);
}
$title = $this->title ? View::escape($this->title) : 'Cases';
echo "
<div class=\"searchResults\">
	<h1>$title</h1>
";
	if (count($this->caseList)) {
		echo "
		<table>
			<thead>
				<tr>
		";
				$fields = CaseList::getDisplayableFields();
				foreach ($this->fields as $name=>$value) {
					echo "<th>$fields[$name]</th>";
				}
		echo "
					<th></th>
				</tr>
			</thead>
			<tbody>
		";
			foreach ($this->caseList as $case) {
				echo '<tr>';
				foreach ($this->fields as $field=>$value) {
					$get = 'get'.ucfirst($field);
					$value = $case->$get();
					if (preg_match('/Person/',$field) || $field=='constituent') {
						if ($value) {
							$value = $value['fullname'];
						}
					}
					if (false !== strpos($field,'Date')) {
						if (isset($value->sec)) {
							$value = date(DATE_FORMAT,$value->sec);
						}
					}
					if ($field=='categories') {
						$c = array();
						foreach ($value as $category) {
							$c[] = $category['name'];
						}
						$value = implode(',',$c);
					}
					$value = View::escape($value);
					echo "<td><a href=\"{$case->getURL()}\">$value</a></td>";
				}
				echo '</tr>';
			}
		echo "
			</tbody>
		</table>
		";
	}
	else {
		echo "<h2>No cases found</h2>";
	}
echo "
</div>
";
