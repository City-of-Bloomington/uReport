<div class="loginForm.inc">
	<form method="post" action="<?php echo BASE_URL; ?>/login/login">
		<fieldset><legend><?php echo $this->translate('labels.login'); ?></legend>
			<input name="return_url" type="hidden" value="<?php echo $this->return_url ?>" />
			<table>
				<tr><th><label for="username"><?php echo $this->translate('labels.username'); ?>:</label></th>
					<td><input name="username" id="username" /></td>
				</tr>
				<tr><th><label for="password"><?php echo $this->translate('labels.password'); ?>:</label></th>
					<td><input type="password" name="password" id="password" /></td>
				</tr>
			</table>
			<button type="submit" class="login"><?php echo $this->translate('labels.login'); ?></button>
		</fieldset>
	</form>
	<script type="text/javascript">
	document.getElementById('username').focus();
	</script>
</div>