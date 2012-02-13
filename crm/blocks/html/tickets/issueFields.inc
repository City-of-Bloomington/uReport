<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Issue $this->issue
 */
?>
<table>
	<tr><td><label for="issue[type]" class="required">Type</label></td>
		<td><select name="issue[type]" id="issue-type">
				<?php
					foreach (Lookups::get('types') as $type) {
						$selected = ($this->issue->getType()==$type)
							? 'selected="selected"'
							: '';
						echo "<option $selected>$type</option>";
					}
				?>
			</select>
		</td>
	</tr>

	<tr class="reportedByPerson">
		<td><label for="issue-reportedByPerson">Reported By Person ID</label></td>
		<td><?php
				$reportedByPerson = $this->issue->getReportedByPerson();
				$id = '';
				$name = '';
				if ($reportedByPerson) {
					$id = $reportedByPerson->getId();
					$name = View::escape($reportedByPerson->getFullname());
				}
			?>
			<input name="issue[reportedByPerson]" id="issue-reportedByPerson"
				value="<?php echo "$id"; ?>" />
		</td>
	</tr>

	<tr><td><label for="issue-contactMethod">Received via</label></td>
		<td><select name="issue[contactMethod]" id="issue-contactMethod">
				<option></option>
				<?php
					foreach (Lookups::get('contactMethods') as $method) {
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
				<option value="">Do not contact</option>
				<?php
					// Use the same drop down as the contact method
					foreach (Lookups::get('contactMethods') as $method) {
						$selected = $this->issue->getContactMethod()==$method
							? 'selected="selected"'
							: '';
						echo "<option $selected>$method</option>";
					}
				?>
			</select>
		</td>
	</tr>

	<tr><td><label>Labels</label></td>
		<td>
		<?php
			foreach (Lookups::get('labels') as $label) {
				$checked = $this->issue->hasLabel($label)
					? 'checked="checked"'
					: '';
				echo "
				<label>
					<input type=\"checkbox\" name=\"issue[labels][]\" value=\"$label\" $checked />
					$label
				</label>
				";
			}
		?>
		</td>
	</tr>
</table>

<div><label for="issue-description">Describe the issue</label></div>
<div>
	<textarea name="issue[description]" id="issue-description"><?php echo View::escape($this->issue->getDescription()); ?></textarea>
</div>

