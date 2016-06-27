<?php
/**
 * @copyright 2013-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Email $this->email
 */
use Application\Models\Email;
?>
<div class="updateEmailForm">
	<form method="post">
		<fieldset><legend><?= $this->_('email'); ?></legend>
			<table>
				<tr><td><label><?= $this->_('label'); ?>
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
					</td>
					<td><input name="email" value="<?= self::escape($this->email); ?>" /></td>
				</tr>
				<tr><td><label for="usedForNotifications"><?= $this->_('usedForNotifications', 'messages'); ?></label></td>
					<td><select name="usedForNotifications" id="usedForNotifications">
						<?php
							foreach (['No', 'Yes'] as $i=>$text) {
								$selected = $this->email->isUsedForNotifications()==$i
									? 'selected="selected"'
									: '';
								echo "<option value=\"$i\" $selected>$text</option>";
							}
						?>
						</select>
					</td>
				</tr>
			</table>
		</fieldset>
		<fieldset>
			<?php
				if ($this->email->getId()) {
					echo "<input type=\"hidden\" name=\"email_id\" value=\"{$this->email->getId()}\" />";
				}
				else {
					echo "<input type=\"hidden\" name=\"person_id\" value=\"{$this->email->getPerson_id()}\" />";
				}

				$cancelUrl = $this->template->controller == 'AccountController'
					? BASE_URI.'/account'
					: $this->email->getPerson()->getUrl();

				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons($cancelUrl);
			?>
		</fieldset>
	</form>
</div>
