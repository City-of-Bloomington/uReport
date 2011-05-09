<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Case $this->case
 * @param Issue $this->issue
 */
?>
<div class="addCaseForm">
	<h1>New Case</h1>
	<form method="post" action="<?php echo BASE_URL; ?>/cases/addCase.php">
		<fieldset class="caseFields">
			<legend>Case Info</legend>
			<table>
				<tr><td><label for="case-location">location</label></td>
					<td><input name="case[location]" id="case-location"
							value="<?php echo View::escape($this->case->getLocation()); ?>" />
					</td>
				</tr>
				<tr><td><label for="case-latitude">latitude</label></td>
					<td><input name="case[latitude]" id="case-latitude"
							value="<?php echo $this->case->getLatitude(); ?>" />
					</td>
				</tr>
				<tr><td><label for="case-longitude">longitude</label></td>
					<td><input name="case[longitude]" id="case-longitude"
							value="<?php echo $this->case->getLongitude(); ?>" />
					</td>
				</tr>
				<tr><td><label for="case-address_id">address_id</label></td>
					<td><input name="case[address_id]" id="case-address_id"
							value="<?php echo $this->case->getAddress_id(); ?>" />
					</td>
				</tr>
				<tr><td><label for="case-city">city</label></td>
					<td><input name="case[city]" id="case-city"
							value="<?php echo View::escape($this->case->getCity()); ?>" />
					</td>
				</tr>
				<tr><td><label for="case-state">state</label></td>
					<td><input name="case[state]" id="case-state"
							value="<?php echo View::escape($this->case->getState()); ?>" />
					</td>
				</tr>
				<tr><td><label for="case-zip">zip</label></td>
					<td><input name="case[zip]" id="case-zip"
							value="<?php echo View::escape($this->case->getZip()); ?>" />
					</td>
				</tr>
			</table>
		</fieldset>
		<fieldset class="issueFields">
			<legend>Issue Information</legend>
			<?php
				include APPLICATION_HOME.'/blocks/html/cases/issueFields.inc';
			?>
		</fieldset>
		<fieldset class="assignmentFields">
			<legend>Assignment Information</legend>
			<?php
				include APPLICATION_HOME.'/blocks/html/cases/assignCaseFields.inc';
			?>
		</fieldset>


		<fieldset>
			<?php
				echo $this->template->formButton('Submit','submit','submit');
				echo $this->template->formButton(
					'Cancel','button','cancel',null,"document.location.href='".BASE_URL."/cases';"
				);
			?>
		</fieldset>
	</form>
</div>