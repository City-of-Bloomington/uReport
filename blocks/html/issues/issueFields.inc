<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Issue $this->issue
 */
?>
<table>
	<tr><td><label for="issue[issueType_id]" class="required">Type</label></td>
		<td><select name="issue[issueType_id]" id="issue-issueType_id">
				<?php
					$list = new IssueTypeList();
					$list->find();
					foreach ($list as $type) {
						$name = View::escape($type);
						$selected = ($this->issue->getIssueType_id() && $this->issue->getIssueType_id()==$type->getId())
							? 'selected="selected"'
							: '';
						echo "<option value=\"{$type->getId()}\" $selected>$name</option>";
					}
				?>
			</select>
		</td>
	</tr>

	<tr><td><label for="issue-reportedByPerson_id">Reported By</label></td>
		<td><input type="hidden" name="issue[reportedByPerson_id]" id="issue-reportedByPerson_id" value="<?php echo $this->issue->getReportedByPerson_id(); ?>" />
			<?php
				echo $this->issue->getReportedByPerson_id()
					? View::escape($this->issue->getReportedByPerson()->getFullname())
					: '';

				$return_url = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
				$url = BASE_URL."/people?return_url=$return_url";
				echo $this->template->formButton(
					'Change Person','button','change',null,
					"document.location.href='$url';"
				);
			?>
		</td>
	</tr>

	<tr><td><label for="issue-contactMethod_id">Contact Method</label></td>
		<td><select name="issue[contactMethod_id]" id="issue-contactMethod_id">
				<option></option>
				<?php
					$list = new ContactMethodList();
					$list->find();
					foreach ($list as $method) {
						$name = View::escape($method);
						$selected = $this->issue->getContactMethod_id()==$method->getId()
							? 'selected="selected"'
							: '';
						echo "<option value=\"{$method->getId()}\" $selected>$name</option>";
					}
				?>
			</select>
		</td>
	</tr>

	<tr><td><label for="issue-categories">Categories</label></td>
		<td><select name="issue[categories][]" id="issue-categories" multiple="true" size="5">
				<?php
					foreach ($this->issue->getCategories() as $category) {
						$name = View::escape($category);
						$selected = $this->issue->hasCategory($category)
							? 'selected="selected"'
							: '';
						echo "<option value=\"{$category->getId()}\" $selected>$name</option>";
					}

					if ($_SESSION['USER']->getDepartment()) {
						foreach ($_SESSION['USER']->getDepartment()->getCategories() as $category) {
							$name = View::escape($category);
							echo "<option value=\"{$category->getId()}\">$name</option>";
						}
					}

				?>
			</select>
		</td>
	</tr>

	<tr><td><label for="issue-case_number">Case Number</label></td>
		<td><input name="issue[case_number]" id="issue-case_number"
				value="<?php echo View::escape($this->issue->getCase_number()); ?>" />
		</td>
	</tr>


</table>
<div><label for="issue-notes">Comment about the issue</label></div>
<div><textarea name="issue[notes]" id="issue-notes" rows="3" cols="60"><?php echo View::escape($this->issue->getNotes()); ?></textarea></div>
