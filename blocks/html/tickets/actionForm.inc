<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 */
?>
<div class="actionForm">
	<form method="post" action="<?php echo BASE_URL; ?>/tickets/recordAction.php">
		<fieldset>
			<input name="ticket_id" type="hidden" value="<?php echo $this->ticket->getId(); ?>" />
			<table>
				<tr><td><label for="action_id">Action</label></td>
					<td><select name="action_id" id="action_id">
							<option></option>
							<?php
								foreach ($_SESSION['USER']->getDepartment()->getActions() as $action) {
									$name = View::escape($action->getName());
									echo "<option value=\"{$action->getId()}\">$name</option>";
								}
							?>
						</select>
					</td>
				</tr>
			</table>
			<div>
				<textarea name="notes" id="notes" rows="3" cols="50"></textarea>
			</div>
			<?php
				echo $this->template->formButton('Submit','submit','submit');
				echo $this->template->formButton('Cancel','reset','cancel');
			?>
		</fieldset>
	</form>
</div>