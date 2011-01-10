<?php
/**
 * @copyright 2006-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param User $this->user
 */
?>
<h1>Edit <?php echo $this->user->getUsername(); ?></h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
	<fieldset><legend>Login Info</legend>
		<input name="user_id" type="hidden" value="<?php echo $this->user->getId(); ?>" />
		<table>
			<tr><td><label for="user-authenticationMethod">Authentication</label></td>
				<td><select name="user[authenticationMethod]" id="user-authenticationMethod">
						<option <?php if ($this->user->getAuthenticationMethod()=='LDAP') echo "selected=\"selected\""; ?>>
							LDAP
						</option>
						<option <?php if ($this->user->getAuthenticationMethod()=='local') echo "selected=\"selected\""; ?>>
							local
						</option>
					</select>
				</td>
			</tr>
			<tr><td><label for="user-username">Username</label></td>
				<td><input name="user[username]" id="user-username" value="<?php echo View::escape($this->user->getUsername()); ?>" />
				</td>
			</tr>
			<tr><td><label for="user-password">Password</label></td>
				<td><input name="user[password]" id="user-password" /></td>
			</tr>
			<tr><td><label for="user-roles">Roles</label></td>
				<td><select name="user[roles][]" id="user-roles" size="5" multiple="multiple">
					<?php
						$roles = new RoleList();
						$roles->find();
						foreach ($roles as $role) {
							$selected = in_array($role,$this->user->getRoles())
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
