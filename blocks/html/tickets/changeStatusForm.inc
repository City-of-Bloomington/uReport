<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 */
?>
<div class="changeStatusForm">
	<form method="post" action="<?php echo BASE_URL; ?>/tickets/changeStatus.php">
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
			<?php
				echo $this->template->formButton('Submit','submit','submit');
				echo $this->template->formButton(
					'Cancel','button','cancel',null,"document.location.href='{$this->ticket->getURL()}';"
				);
			?>
		</fieldset>
	</form>
</div>