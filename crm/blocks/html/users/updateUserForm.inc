<?php
/**
 * @copyright 2006-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
$title = $this->person->getUsername() ? 'Edit Account' : 'Create Account';
?>
<div class="updateUserForm">
	<h2><?php echo $title; ?></h2>
	<form method="post" action="<?php echo BASE_URL; ?>/users/update">
		<fieldset><legend><?php echo $this->translate('labels.login'); ?></legend>
			<table>
				<tr><td><label for="department_id"><?php echo $this->translate(array('labels.department', 'labels.departments', 1)); ?></label></td>
					<td><select name="department_id" id="department_id">
							<option></option>
						<?php
							$list = new DepartmentList();
							$list->find();
							foreach ($list as $department) {
								$name = View::escape($department->getName());
								$selected = $this->person->getDepartment_id()==$department->getId()
											? 'selected="selected"'
											: '';
								echo "<option value=\"{$department->getId()}\" $selected>$name</option>";
							}
						?>
						</select>
					</td>
				</tr>
				<tr><td><label for="authenticationMethod"><?php echo $this->translate('labels.authenticationMethod'); ?></label></td>
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
				<tr><td><label for="username"><?php echo $this->translate('labels.username'); ?></label></td>
					<td><input name="username" id="username" value="<?php echo View::escape($this->person->getUsername()); ?>" />
					</td>
				</tr>
				<tr><td><label for="password"><?php echo $this->translate('labels.password'); ?></label></td>
					<td><input name="password" id="password" /></td>
				</tr>
				<tr><td><label for="role"><?php echo $this->translate('labels.role'); ?></label></td>
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

			<p><?php echo $this->translate('text.employee_authentication_description'); ?></p>
		</fieldset>

		<?php include APPLICATION_HOME.'/blocks/html/people/personInfoFields.inc'; ?>

		<fieldset><legend><?php echo $this->translate('labels.submit'); ?></legend>
			<?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/users');
			?>
		</fieldset>
	</form>
</div>
