<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param PersonList $this->personList
 * @param string $this->return_url (optional) The url to use for each of th search results
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
			echo '<a class="add button" href="'.BASE_URL.'/people/update">Add a Person</a>';
		}
	?>
</div>
