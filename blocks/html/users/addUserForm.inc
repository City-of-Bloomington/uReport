<?php
/**
 * @copyright 2006-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<h1>New User Account</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
	<fieldset><legend>Login Info</legend>
		<table>
			<tr><td><label for="user-authenticationMethod">Authentication</label></td>
				<td><select name="user[authenticationMethod]" id="user-authenticationMethod">
						<option <?php if(isset($_POST['user']['authenticationMethod']) && $_POST['user']['authenticationMethod']=="LDAP") echo "selected=\"selected\""; ?>>
							LDAP
						</option>
						<option <?php if(isset($_POST['user']['authenticationMethod']) && $_POST['user']['authenticationMethod']=="local") echo "selected=\"selected\""; ?>>
							local
						</option>
					</select>
				</td>
			</tr>
			<tr><td><label for="user-username">Username</label></td>
				<td><input name="user[username]" id="user-username" value="<?php if(isset($_POST['user']['username'])) echo View::escape($_POST['user']['username']); ?>" />
				</td>
			</tr>
			<tr><td><label for="user-password">Password</label></td>
				<td><input name="user[password]" id="user-password" /></td>
			</tr>
			<tr><td><label for="user[roles]">Roles</label></td>
				<td><select name="user[roles][]" id="user-roles" size="5" multiple="multiple">
					<?php
						$roles = new RoleList();
						$roles->find();
						foreach ($roles as $role) {
							$selected = (isset($_POST['user']['roles']) && in_array($role,$_POST['user']['roles']))
										? 'selected="selected"'
										: '';
							echo "<option $selected>$role</option>";
						}
					?>
					</select>
				</td>
			</tr>
		</table>

		<?php
			echo $this->formButton('Submit','submit','submit');
			echo $this->formButton(
				'Cancel','button','cancel',null,"document.location.href='".BASE_URL."/users';"
			);
		?>
	</fieldset>
</form>
