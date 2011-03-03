<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param PersonList $this->personList
 * @param string $this->return_url (optional) The url to use for each of th search results
 */
?>
<div class="searchResults">
	<h1>Search Results</h1>
	<?php
		if (count($this->personList)) {
			include APPLICATION_HOME.'/blocks/html/people/personList.inc';
		}
		else {
			echo '<p>No results found</p>';
		}

		if (userIsAllowed('People')) {
			$url = new URL(BASE_URL.'/people/updatePerson.php');
			if ($this->return_url) {
				$url->return_url = $this->return_url;
			}
			echo $this->formButton(
				'Add Person','button','add',null,"document.location.href='$url';"
			);
		}
	?>
</div>