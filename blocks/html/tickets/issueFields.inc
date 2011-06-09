<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Issue $this->issue
 */
?>
<table>
	<tr><td><label for="issue[type]" class="required">Type</label></td>
		<td><select name="issue[type]" id="issue-type">
				<?php
					foreach (Issue::$types as $type) {
						$selected = ($this->issue->getType()==$type)
							? 'selected="selected"'
							: '';
						echo "<option $selected>$type</option>";
					}
				?>
			</select>
		</td>
	</tr>

	<tr><td><label for="issue-reportedByPerson">Reported By</label></td>
		<td><?php
				$reportedByPerson = $this->issue->getReportedByPerson();
				$reportedByPerson_id = isset($reportedByPerson['_id'])
					? $reportedByPerson['_id']
					: '';
				$fullname = isset($reportedByPerson['fullname'])
					? $reportedByPerson['fullname']
					: '';
			?>
			<input type="hidden" name="issue[reportedByPerson]" id="issue-reportedByPerson"
				value="<?php echo "$reportedByPerson_id"; ?>" />
			<?php
				echo $fullname;
				$return_url = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
				$url = BASE_URL."/people?return_url=$return_url";
				echo $this->template->formButton(
					'Change Person','button','change',null,
					"document.location.href='$url';"
				);
			?>
		</td>
	</tr>

	<tr><td><label for="issue-contactMethod">Received via</label></td>
		<td><select name="issue[contactMethod]" id="issue-contactMethod">
				<option></option>
				<?php
					foreach (Issue::$contactMethods as $method) {
						$selected = $this->issue->getContactMethod()==$method
							? 'selected="selected"'
							: '';
						echo "<option $selected>$method</option>";
					}
				?>
			</select>
		</td>
	</tr>

	<tr><td><label for="issue-responseMethod">Response Method</label></td>
		<td><select name="issue[responseMethod]" id="issue-responseMethod">
				<option></option>
				<?php
					// Use the same drop down as the contact method
					foreach (Issue::$contactMethods as $method) {
						$selected = $this->issue->getContactMethod()==$method
							? 'selected="selected"'
							: '';
						echo "<option $selected>$method</option>";
					}
				?>
			</select>
		</td>
	</tr>

	<tr><td><label for="issue-category">Category</label></td>
		<td><select name="issue[category]" id="issue-category">
			<?php
				$issueCategory_id = null;
				$cat = $this->issue->getCategory();
				if ($cat) {
					$issueCategory_id = "$cat[_id]";
					$name = View::escape($cat['name']);
					echo "<option value=\"$issueCategory_id\">$name</option>";
				}
				if ($_SESSION['USER']->getDepartment()) {
					$dept = $_SESSION['USER']->getDepartment();
					$department = new Department($dept['_id']);

					foreach ($department->getCategories() as $departmentCategory) {
						$selected = $issueCategory_id=="$departmentCategory[_id]"
							? 'selected="selected"'
							: '';
						$name = View::escape($departmentCategory['name']);
						echo "<option value=\"$departmentCategory[_id]\">$name</option>";
					}
				}

			?>
			</select>
			<div>
				<?php
					$url = new URL(BASE_URL.'/categories/chooseCategory.php');
					$url->return_url = "{$this->return_url}";
					echo "<a href=\"$url\">Choose from all categories</a>";
				?>
			</div>
		</td>
	</tr>
</table>

<div><label for="issue-notes">Comment about the issue</label></div>
<div>
	<textarea name="issue[notes]" id="issue-notes" rows="3" cols="60"><?php echo View::escape($this->issue->getNotes()); ?></textarea>
</div>
