<?php
/**
 * @copyright 2011-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 */
use Application\Models\SubstatusTable;
use Blossom\Classes\View;
?>
<div class="changeStatusForm">
	<form method="post" action="<?php echo BASE_URL; ?>/tickets/changeStatus">
		<fieldset><legend><?php echo $this->translate('labels.change_status'); ?></legend>
			<input name="ticket_id" type="hidden" value="<?php echo $this->ticket->getId(); ?>" />
			<?php
				// Preserve any extra parameters passed in
				foreach ($_REQUEST as $key=>$value) {
					if ($key!='ticket_id' && $key!='return_url') {
						$key = View::escape($key);
						$value = View::escape($value);
						echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />\n";
					}
				}
			?>
			<table>
				<tr><td><label for="status"><?php echo $this->translate(array('labels.status', 'labels.statuses', 1)); ?></label></td>
					<td><select name="status" id="status">
						<?php
							foreach (array('open', 'closed') as $status) {
								$selected = ($this->ticket->getStatus()==$status)
									? 'selected="selected"'
									: '';
								echo "<option $selected>$status</option>";
							}
						?>
						</select>
					</td>
				</tr>

				<tr><td><label for="substatus_id"><?php echo $this->translate(array('labels.substatus', 'labels.substatuses', 1)); ?></label></td>
					<td><select name="substatus_id" id="substatus_id">
						<?php
							// Open tickets do not require substatus
							// Closed tickets must have a substatus
							if ($this->ticket->getStatus()=='open') {
								echo '<option value=""></option>';
							}

							$table = new SubstatusTable();
							$list = $table->find(array('status'=>$this->ticket->getStatus()));
							foreach ($list as $substatus) {
								$selected = $this->ticket->getSubstatus_id()==$substatus->getId()
									? 'selected="selected"'
									: '';
								$name = View::escape($substatus->getName());
								echo "<option value=\"{$substatus->getId()}\" $selected>$name</option>";
							}
						?>
						</select>
					</td>
				</tr>

				<tr><td><label id="notes" class="required">
							<?php echo $this->translate(array('labels.comment', 'labels.comments', 1)); ?>
							<span class="icon-asterisk"></span>
						</label>
					</td>
					<td><textarea id="notes" name="notes"></textarea></td>
				</tr>
			</table>
			<?php
				include APPLICATION_HOME.'/blocks/html/errorMessages/tickets/missingClosingComment.inc';
				include APPLICATION_HOME.'/blocks/html/tickets/partials/submitAndCancelButtons.inc';
			?>
		</fieldset>
	</form>
</div>
<?php
	$this->template->addToAsset('scripts',YUI.'/yui/yui-min.js');
	$this->template->addToAsset('scripts',BASE_URI.'/js/tickets/chooseStatus.js');
