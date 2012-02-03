<?php
/**
 * @copyright 2006-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
$title = $this->person->getUsername() ? 'Edit Account' : 'Create Account';
?>
<div class="updateUserForm">
	<h2><?php echo $title; ?></h2>
	<form method="post" action="<?php echo BASE_URL; ?>/users/update">
		<fieldset><legend>Login Info</legend>
			<input name="person_id" type="hidden" value="<?php echo $this->person->getId(); ?>" />
			<table>
				<tr><td><label for="department">Department</label></td>
					<td><select name="department" id="department">
							<option></option>
						<?php
							$list = new DepartmentList();
							$list->find();
							foreach ($list as $department) {
								$name = View::escape($department->getName());
								$selected = "{$this->person->getDepartment_id()}"=="{$department->getId()}"
											? 'selected="selected"'
											: '';
								echo "<option value=\"{$department->getId()}\" $selected>$name</option>";
							}
						?>
						</select>
					</td>
				</tr>
				<tr><td><label for="authenticationMethod">Authentication</label></td>
					<td><select name="authenticationMethod" id="authenticationMethod">
						<?php
							foreach (Person::getAuthenticationMethods() as $method) {
								$selected = $this->person->getAuthenticationMethod()==$method
									? 'selected="selected"'
									: '';
								echo "<option $selected>$method</option>";
							}
						?>
						</select>
					</td>
				</tr>
				<tr><td><label for="username">Username</label></td>
					<td><input name="username" id="username" value="<?php echo View::escape($this->person->getUsername()); ?>" />
					</td>
				</tr>
				<tr><td><label for="password">Password</label></td>
					<td><input name="password" id="password" /></td>
				</tr>
				<tr><td><label for="role">Role</label></td>
					<td><select name="role" id="role"><option></option>
						<?php
							global $ZEND_ACL;
							foreach (array_reverse($ZEND_ACL->getRoles()) as $role) {
								$selected = $this->person->getRole()==$role
									? 'selected="selected"'
									: '';
								echo "<option $selected>$role</option>";
							}
						?>
						</select>
					</td>
				</tr>
			</table>
			<button type="submit" class="submit">Submit</button>
			<button type="button" class="cancel"
				onclick="document.location.href='<?php echo BASE_URI; ?>/users';">
				Cancel
			</button>
		</fieldset>
	</form>
</div>