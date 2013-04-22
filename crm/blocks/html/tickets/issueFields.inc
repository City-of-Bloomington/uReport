<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Issue $this->issue
 */
?>
<table>
	<tr><td><label for="issueType_id" class="required">Type <span class="icon-asterisk"></span></label></td>
		<td><select name="issueType_id" id="issueType_id">
				<?php
					$list = new IssueTypeList();
					$list->find();
					foreach ($list as $type) {
						$selected = $this->issue->getIssueType_id()==$type->getId()
							? 'selected="selected"'
							: '';
						$name = View::escape($type->getName());
						echo "<option value=\"{$type->getId()}\" $selected>$type</option>";
					}
				?>
			</select>
		</td>
	</tr>

	<tr class="reportedByPerson">
		<td><label for="reportedByPerson_id">Reported By Person</label></td>
		<td><?php
				$reportedByPerson = $this->issue->getReportedByPerson();
				$helper = $this->template->getHelper('personChooser');
				echo $helper->personChooser('reportedByPerson', $reportedByPerson);
			?>
		</td>
	</tr>

	<tr><td><label for="contactMethod_id">Received via</label></td>
		<td><select name="contactMethod_id" id="contactMethod_id">
				<option></option>
				<?php
					$contactMethods = new ContactMethodList();
					$contactMethods->find();
					foreach ($contactMethods as $method) {
						$selected = $this->issue->getContactMethod_id()==$method->getId()
							? 'selected="selected"'
							: '';
						$name = View::escape($method->getName());
						echo "<option value=\"{$method->getId()}\" $selected>$method</option>";
					}
				?>
			</select>
		</td>
	</tr>

	<tr><td><label for="responseMethod_id">Response Method</label></td>
		<td><select name="responseMethod_id" id="responseMethod_id">
				<option value=""></option>
				<?php
					// Use the same drop down as the contact method
					foreach ($contactMethods as $method) {
						$selected = $this->issue->getResponseMethod_id()==$method->getId()
							? 'selected="selected"'
							: '';
						$name = View::escape($method->getName());
						echo "<option value=\"{$method->getId()}\" $selected>$name</option>";
					}
				?>
			</select>
		</td>
	</tr>

	<tr><td><label>Labels</label></td>
		<td>
		<?php
			$list = new LabelList();
			$list->find();
			foreach ($list as $label) {
				$checked = $this->issue->hasLabel($label)
					? 'checked="checked"'
					: '';
				$name = View::escape($label->getName());
				echo "
				<label>
					<input type=\"checkbox\" name=\"labels[]\" value=\"{$label->getId()}\" $checked />
					$name
				</label>
				";
			}
		?>
		</td>
	</tr>
</table>

<div><label for="description">Describe the issue</label></div>
<div>
	<textarea name="description" id="description"><?php echo View::escape($this->issue->getDescription()); ?></textarea>
</div>
