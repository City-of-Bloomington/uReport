<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 */
$department = new Department($_SESSION['USER']->getData('department._id'));
$actions = $department->getActions();
if (count($actions)) {
?>
<div class="actionForm">
	<form method="post" action="<?php echo BASE_URL; ?>/tickets/recordAction.php">
		<fieldset>
			<input name="ticket_id" type="hidden" value="<?php echo $this->ticket->getId(); ?>" />
			<table>
				<tr><td><label for="action">Action</label></td>
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
				</tr>
				<tr><td><label for="actionDate">Date</label></td>
					<td><input name="actionDate" id="actionDate"
							value="<?php echo date(DATE_FORMAT); ?>" />
					</td>
				</tr>
				<tr>
					<td><label for="notes">Comment</label></td>
					<td><textarea name="notes" id="notes" rows="3" cols="50"></textarea></td>
				</tr>
			</table>
			<?php
				echo $this->template->formButton('Submit','submit','submit');
				echo $this->template->formButton('Cancel','reset','cancel');
			?>
		</fieldset>
	</form>
</div>
<?php
}
