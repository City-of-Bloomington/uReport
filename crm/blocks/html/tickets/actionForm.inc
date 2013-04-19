<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 */
$department = $_SESSION['USER']->getDepartment_id()
	? $_SESSION['USER']->getDepartment()
	: new Department();
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
				<tr><td><label for="action_id" class="required">Action <span class="icon-asterisk"></span></label></td>
					<td><select name="action_id" id="action_id">
							<option></option>
							<?php
								foreach ($actions as $action) {
									$name = View::escape($action->getName());
									echo "<option value=\"{$action->getId()}\">$name</option>";
								}
							?>
						</select>
					</td>
				</tr>
				<tr><td><label for="actionDate">Date</label>
				<p class="help"><?php
	echo str_replace(array('Y','m','d'), array('yyyy','mm','dd'), DATE_FORMAT);
?></p></td>
					<td><input name="actionDate" id="actionDate"
							value="<?php echo date(DATE_FORMAT); ?>" />
					</td>
				</tr>
			</table>
		</fieldset>
		<fieldset>
			<div><label for="notes">Comments</label></div>
			<div><textarea name="notes" id="notes"></textarea></div>
		</fieldset>
		<fieldset class="buttons">
			<button type="submit"><span class="icon-ok"></span> Submit</button>
			<button type="reset"><span class="icon-remove"></span> Cancel</button>
		</fieldset>
	</form>
</div>
<?php
}
