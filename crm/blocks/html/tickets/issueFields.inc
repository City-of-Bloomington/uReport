<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Issue $this->issue
 */
use Application\Models\ContactMethodTable;
use Application\Models\IssueTypeTable;
use Application\Models\LabelTable;
use Blossom\Classes\View;
?>
<table>
	<tr><td><label for="issueType_id" class="required">
				<?= $this->_('labels.type'); ?>
				<span class="fa fa-asterisk"></span>
			</label>
		</td>
		<td><select name="issueType_id" id="issueType_id">
				<?php
					$table = new IssueTypeTable();
					$list = $table->find();
					foreach ($list as $type) {
						$selected = $this->issue->getIssueType_id()==$type->getId()
							? 'selected="selected"'
							: '';
						$name = self::escape($type->getName());
						echo "<option value=\"{$type->getId()}\" $selected>$type</option>";
					}
				?>
			</select>
		</td>
	</tr>

	<tr class="reportedByPerson">
		<td><label for="reportedByPerson_id"><?= $this->_('labels.reportedByPerson'); ?></label></td>
		<td><?php
				$reportedByPerson = $this->issue->getReportedByPerson();
				$helper = $this->template->getHelper('personChooser');
				echo $helper->personChooser('reportedByPerson', $reportedByPerson);
			?>
		</td>
	</tr>

	<tr><td><label for="contactMethod_id">
			<?= $this->_(array('labels.contactMethod', 'labels.contactMethods', 1)); ?>
			</label>
		</td>
		<td><select name="contactMethod_id" id="contactMethod_id">
				<option></option>
				<?php
					$table = new ContactMethodTable();
					$contactMethods = $table->find();
					$contactMethods->buffer();
					foreach ($contactMethods as $method) {
						$selected = $this->issue->getContactMethod_id()==$method->getId()
							? 'selected="selected"'
							: '';
						$name = self::escape($method->getName());
						echo "<option value=\"{$method->getId()}\" $selected>$method</option>";
					}
				?>
			</select>
		</td>
	</tr>

	<tr><td><label for="responseMethod_id"><?= $this->_('labels.responseMethod'); ?></label></td>
		<td><select name="responseMethod_id" id="responseMethod_id">
				<option value=""></option>
				<?php
					// Use the same drop down as the contact method
					foreach ($contactMethods as $method) {
						$selected = $this->issue->getResponseMethod_id()==$method->getId()
							? 'selected="selected"'
							: '';
						$name = self::escape($method->getName());
						echo "<option value=\"{$method->getId()}\" $selected>$name</option>";
					}
				?>
			</select>
		</td>
	</tr>

</table>

<div><label for="description"><?= $this->_('labels.description'); ?></label></div>
<div>
	<textarea name="description" id="description"><?= self::escape($this->issue->getDescription()); ?></textarea>
</div>
