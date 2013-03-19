<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param PersonList $this->personList
 * @param bool $this->disableButtons
 */
?>
<div class="findPeopleResults">
	<h2>Search Results</h2>
	<?php
		if (count($this->personList)) {
			include APPLICATION_HOME.'/blocks/html/people/personList.inc';
		}
		else {
			echo '<p>No results found</p>';
		}
		if (!$this->disableButtons && userIsAllowed('people','add')) {
			include APPLICATION_HOME.'/blocks/html/people/personSelectForMerge.inc';


			// Preserve any extra parameters passed in
			$url = new URL(BASE_URL.'/people/update');
			$fields = array('firstname','lastname','email','organization','setOfPeople');
			foreach ($_REQUEST as $key=>$value) {
				if (!in_array($key,$fields)) {
					$url->$key = $value;
				}
			}
			echo "<a class=\"add button\" href=\"$url\">Add a Person</a>";
		}
	?>
</div>
