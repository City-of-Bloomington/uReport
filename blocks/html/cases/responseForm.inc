<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Issue $this->issue
 */
$person = View::escape($_SESSION['USER']->getPerson()->getFullname());
$constituent = View::escape($this->issue->getReportedByPerson()->getFullname());
$date = new Date();
$description = View::escape("$person resonded to $constituent");
?>
<div class="responseForm">
	<h1>Respond</h1>
	<p><?php echo $description; ?></p>
	<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
		<fieldset><legend></legend>
			<input name="issue_id" type="hidden" value="<?php echo $this->issue->getId(); ?>" />
			<table>
				<tr><td><label for="contactMethod_id">Contact Method</label></td>
					<td><select name="contactMethod_id" id="contactMethod_id">
							<?php
								$list = new ContactMethodList();
								$list->find();
								foreach ($list as $method) {
									$name = View::escape($method);
									echo "<option value=\"{$method->getId()}\">$name</option>";
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
					'Cancel','button','cancel',null,"document.location.href='{$this->issue->getCase()->getURL()}';"
				);
			?>
		</fieldset>
	</form>
</div>