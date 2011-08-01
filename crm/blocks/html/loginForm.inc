<form method="post" action="<?php echo BASE_URL; ?>/login/login.php">
	<fieldset><legend>Login</legend>
		<input name="return_url" type="hidden" value="<?php echo $this->return_url ?>" />
		<table>
			<tr><th><label for="username">Username:</label></th>
				<td><input name="username" id="username" /></td><td></td>
			</tr>
			<tr><th><label for="password">Password:</label></th>
				<td><input type="password" name="password" id="password" /></td>
			</tr>
		</table>
		<?php
			echo $this->formButton('Login','submit','login');
		?>
	</fieldset>
</form>
<script type="text/javascript">
document.getElementById('username').focus();
</script>