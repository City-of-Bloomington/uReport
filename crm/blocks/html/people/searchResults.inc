<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param PersonList $this->personList
 * @param string $this->return_url (optional) The url to use for each of th search results
 */
?>
<div class="findPeopleResults">
	<h1>Search Results</h1>
	<?php
		if (count($this->personList)) {
			include APPLICATION_HOME.'/blocks/html/people/personList.inc';
		}
		else {
			echo '<p>No results found</p>';
		}
		if (userIsAllowed('People')) {
			echo '<a class="add button" href="'.BASE_URL.'/people/updatePerson.php">Add a Person</a>';
		}
	?>
</div>
