<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
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

		if (!$this->disableButtons && count($this->personList)>1) {
			echo "
			<form method=\"get\" action=\"".BASE_URL."/people/mergePeople.php\">
				<fieldset><legend>Merge Two People</legend>
					<p>Add two people IDs to compare and move data between them.</p>
					<div>
						<input name=\"person_id_a\" id=\"person_id_a\" />
						<input name=\"person_id_b\" id=\"person_id_b\" />
			";
							echo $this->template->formButton(
								'Submit','submit','submit'
							);
			echo "
					</div>
				</fieldset>
			</form>
			";
		}
	?>
</div>
