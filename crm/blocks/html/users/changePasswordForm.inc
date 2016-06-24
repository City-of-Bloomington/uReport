<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<div id="changePasswordForm">
	<h2><?= $this->_('labels.change_password'); ?></h2>
	<form method="post" action="<?= BASE_URI; ?>/account/changePassword">
		<fieldset>
			<table>
				<tr><td><label for="current_password" class="required"><?= $this->_('labels.current_password'); ?></label></td>
					<td><input name="current_password" id="current_password" type="password" /></td>
				</tr>
				<tr><td><label for="new_password" class="required"><?= $this->_('labels.new_password'); ?></label></td>
					<td><input name="new_password" id="new_password" type="password" /></td>
				</tr>
				<tr><td><label for="retype_password" class="required"><?= $this->_('labels.retype_password'); ?></label></td>
					<td><input name="retype_password" id="retype_password" type="password" /></td>
				</tr>
			</table>
			<?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/account');
			?>
		</fieldset>
	</form>
</div>
