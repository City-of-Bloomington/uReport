<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Issue $this->issue
 */
?>
<div class="responseForm">
	<h1>Record a response</h1>
	<form method="post" action="<?php echo $_SERVER['REDIRECT_URL']; ?>">
		<fieldset><legend>How did you respond to the person?</legend>
			<input name="issue_id" type="hidden" value="<?php echo $this->issue->getId(); ?>" />
			<table>
				<tr><td><label for="contactMethod_id">Contact Method</label></td>
					<td><select name="contactMethod_id" id="contactMethod_id">
						<?php
							$list = new ContactMethodList();
							$list->find();
							foreach ($list as $method) {
								$name = View::escape($method->getName());
								$selected = $method->getId() == $this->issue->getContactMethod_id()
									? 'selected="selected"'
									: '';
								echo "<option value=\"{$method->getId()}\" $selected>$name</option>";
							}
						?>
						</select>
					</td>
				</tr>
			</table>
			<div><label for="notes">Notes</label></div>
			<div><textarea name="notes" id="notes"></textarea></div>
			<button type="submit" class="submit">Submit</button>
			<button type="button" class="cancel"
				onclick="document.location.href='<?php echo $this->issue->getTicket()->getURL(); ?>';">
				Cancel
			</button>
		</fieldset>
	</form>
</div>