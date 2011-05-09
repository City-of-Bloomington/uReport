<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param CaseList $this->caseList
 * @param string $this->title
 * @param bool $this->disableButtons
 * @param Case $this->filterCase A case to leave out of the display
 */
?>
<div class="caseList">
	<h1><?php echo $this->title ? View::escape($this->title) : 'Cases'; ?></h1>
	<table>
		<thead>
			<tr><th>ID</th>
				<th>Status</th>
				<th>Location</th>
				<th>Entered Date</th>
				<th>Entered By</th>
				<th>Categories</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($this->caseList as $case) {
				if ($this->filterCase
					&& "{$this->filterCase->getId()}"=="{$case->getId()}") {
					continue;
				}
				$status = View::escape($case->getStatus());
				$location = View::escape($case->getLocation());
				$enteredBy = View::escape($case->getData('enteredByPerson.fullname'));
				echo "
				<tr><td><a href=\"{$case->getURL()}\">{$case->getId()}</a></td>
					<td>$status</td>
					<td>$location</td>
					<td>{$case->getEnteredDate(DATE_FORMAT)}</td>
					<td>$enteredBy</td>
					<td>
				";
					$categories = $case->getCategories();
					if (count($categories)) {
						echo '<ul>';
						foreach ($categories as $category) {
							echo '<li>'.View::escape($category['name']).'</li>';
						}
						echo '</ul>';

					}
					if (!$this->disableButtons && userIsAllowed('Issues')) {
						echo '<td>';
						$url = new URL(BASE_URL.'/cases/updateIssue.php');
						$url->case_id = "{$case->getId()}";
						if (isset($_GET['person_id'])) {
							try {
								$person = new Person($_GET['person_id']);
								$url->person_id = "{$person->getId()}";
							}
							catch (Exception $e) {
								// Just ignore invalid people
							}
						}
						echo $this->template->linkButton(
							'Add Issue to case',$url,'add',null,null,'button'
						);
						echo '</td>';
					}
				echo "
					</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
	<?php
		if (!$this->disableButtons && count($this->caseList)>1) {
			echo "
			<form method=\"get\" action=\"".BASE_URL."/cases/mergeCases.php\">
				<fieldset><legend>Merge Two Cases</legend>
					<p>Add two case IDs to compare cases and move data between them.</p>
					<div>
						<input name=\"case_id_a\" id=\"case_id_a\" />
						<input name=\"case_id_b\" id=\"case_id_b\" />
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
