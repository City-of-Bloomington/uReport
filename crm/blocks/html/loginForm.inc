<div class="loginForm.inc">
	<form method="post">
		<fieldset><legend><?= $this->_('login'); ?></legend>
			<input name="return_url" type="hidden" value="<?= $this->return_url ?>" />
			<table>
				<tr><th><label for="username"><?= $this->_('username'); ?>:</label></th>
					<td><input name="username" id="username" /></td>
				</tr>
				<tr><th><label for="password"><?= $this->_('password'); ?>:</label></th>
					<td><input type="password" name="password" id="password" /></td>
				</tr>
			</table>
			<button type="submit" class="login"><?= $this->_('login'); ?></button>
		</fieldset>
	</form>
	<script type="text/javascript">
	document.getElementById('username').focus();
	</script>
</div>
