<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 * @param int $this->index
 */
?>
<div class="responseForm">
	<h1>Record a response</h1>
	<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
		<fieldset><legend>How did you respond to the person?</legend>
			<input name="ticket_id" type="hidden" value="<?php echo $this->ticket->getId(); ?>" />
			<input name="index" type="hidden" value="<?php echo $this->index; ?>" />
			<table>
				<tr><td><label for="contactMethod">Contact Method</label></td>
					<td><select name="contactMethod" id="contactMethod">
							<?php
								foreach (Issue::$contactMethods as $method) {
									$method = View::escape($method);
									echo "<option>$method</option>";
								}
							?>
						</select>
					</td>
				</tr>
			</table>
			<div><label for="notes">Notes</label></div>
			<div><textarea name="notes" id="notes" rows="3" cols="60"></textarea></div>
			<?php
				echo $this->template->formButton('Submit','submit','submit');
				echo $this->template->formButton(
					'Cancel','button','cancel',null,"document.location.href='{$this->ticket->getURL()}';"
				);
			?>
		</fieldset>
	</form>
</div>