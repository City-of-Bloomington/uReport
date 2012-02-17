<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 */
$department = new Department($_SESSION['USER']->getData('department._id'));
$actions = $department->getActions();
if (count($actions)) {
?>
<div class="actionForm">
	<h1>Case Actions</h1>
	<p>Update the history log with actions performed on this case</p>
	<form method="post" action="<?php echo BASE_URL; ?>/tickets/recordAction">
		<fieldset>
			<input name="ticket_id" type="hidden" value="<?php echo $this->ticket->getId(); ?>" />
			<table>
				<tr><td><label for="action">Action</label></td>
					<td><label for="actionDate">Date</label></td>
					<td><label for="notes">Comment</label></td>
				</tr>
				<tr>
					<td><select name="action" id="action">
							<option></option>
							<?php
								foreach ($department->getActions() as $action) {
									$name = View::escape($action['name']);
									echo "<option>$name</option>";
								}
							?>
						</select>
					</td>
					<td><input name="actionDate" id="actionDate"
							value="<?php echo date(DATE_FORMAT); ?>" />
					</td>
					<td colspan="2"><textarea name="notes" id="notes"></textarea></td>
				</tr>
			</table>
			<button type="submit" class="submit">Submit</button>
			<button type="reset" class="cancel">Cancel</button>
		</fieldset>
	</form>
</div>
<?php
}
