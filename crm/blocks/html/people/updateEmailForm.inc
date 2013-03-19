<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Email $this->email
 */
?>
<div class="updateEmailForm">
	<form method="post" action="<?php echo BASE_URI; ?>/people/updateEmail">
		<fieldset><legend>Email Address</legend>
			<label>
				Label
				<select name="label" id="label">
				<?php
					foreach (Email::$LABELS as $label) {
						$selected = $this->email->getLabel()== $label
							? 'selected="selected"'
							: '';
						echo "<option $selected>$label</option>";
					}
				?>
				</select>
			</label>
			<input name="email" value="<?php echo View::escape($this->email->getEmail()); ?>" />
		</fieldset>
		<fieldset>
			<?php
				if ($this->email->getId()) {
					echo "<input type=\"hidden\" name=\"email_id\" value=\"{$this->email->getId()}\" />";
				}
				else {
					echo "<input type=\"hidden\" name=\"person_id\" value=\"{$this->email->getPerson_id()}\" />";
				}
			?>
			<button type="submit" class="save">Save</button>
			<a class="cancel" href="<?php echo $this->email->getPerson()->getURL(); ?>">Cancel</a>
		</fieldset>
	</form>
</div>