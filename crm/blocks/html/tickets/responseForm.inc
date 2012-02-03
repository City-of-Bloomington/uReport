<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 * @param int $this->index
 */
?>
<div class="responseForm">
	<h1>Record a response</h1>
	<form method="post" action="<?php echo $_SERVER['REDIRECT_URL']; ?>">
		<fieldset><legend>How did you respond to the person?</legend>
			<input name="ticket_id" type="hidden" value="<?php echo $this->ticket->getId(); ?>" />
			<input name="index" type="hidden" value="<?php echo $this->index; ?>" />
			<table>
				<tr><td><label for="contactMethod">Contact Method</label></td>
					<td><select name="contactMethod" id="contactMethod">
							<?php
								foreach (Lookups::get('contactMethods') as $method) {
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
			<button type="submit" class="submit">Submit</button>
			<button type="button" class="cancel"
				onclick="document.location.href='<?php echo $this->ticket->getURL(); ?>';">
				Cancel
			</button>
		</fieldset>
	</form>
</div>