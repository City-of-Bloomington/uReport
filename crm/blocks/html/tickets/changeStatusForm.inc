<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 */
?>
<div class="changeStatusForm">
	<form method="post" action="<?php echo BASE_URL; ?>/tickets/changeStatus">
		<fieldset><legend>Change Status</legend>
			<input name="ticket_id" type="hidden" value="<?php echo $this->ticket->getId(); ?>" />
			<table>
				<tr><td><label for="status">Status</label></td>
					<td><select name="status" id="status">
							<option>open</option>
							<?php
								$dept = $_SESSION['USER']->getDepartment();
								if ($dept) {
									$department = new Department($dept['_id']);

									foreach ($department->getCustomStatuses() as $status) {
										$selected = $this->ticket->getStatus()==$status
											? 'selected="selected"'
											: '';
										$status = View::escape($status);
										echo "<option $selected>$status</option>";
									}
								}
							?>
						</select>
					</td>
				</tr>
				<tr><td><label id="notes">Comment</label></td>
					<td><textarea id="notes" name="notes" rows="3" cols="50"></textarea></td>
				</tr>
			</table>
			<button type="submit" class="submit">Submit</button>
			<button type="button" class="cancel"
				onclick="document.location.href='<?php echo $this->ticket->getURL(); ?>';">
				Cancel
			</button>
		</fieldset>
	</form>
</div>