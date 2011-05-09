<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Case $this->case
 */
?>
<div class="closeCaseForm">
	<form method="post" action="<?php echo BASE_URL; ?>/cases/closeCase.php">
		<fieldset><legend>Close This Case</legend>
			<input name="case_id" type="hidden" value="<?php echo $this->case->getId(); ?>" />
			<table>
				<tr><td><label for="resolution">Resolution</label></td>
					<td><select name="resolution" id="resolution">
						<?php
							$list = new ResolutionList();
							$list->find();
							foreach ($list as $resolution) {
								$name = View::escape($resolution->getName());
								echo "<option>$name</option>";
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
					'Cancel','button','cancel',null,"document.location.href='{$this->case->getURL()}';"
				);
			?>
		</fieldset>
	</form>
</div>