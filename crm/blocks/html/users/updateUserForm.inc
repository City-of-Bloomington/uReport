<?php
/**
 * @copyright 2006-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
use Application\Models\DepartmentTable;
use Application\Models\Person;
use Blossom\Classes\View;

$title = $this->person->getUsername() ? 'Edit Account' : 'Create Account';
?>
<div class="updateUserForm">
	<h2><?= $title; ?></h2>
	<form method="post" action="<?= BASE_URL; ?>/users/update">
		<fieldset><legend><?= $this->_('login'); ?></legend>
			<table>
				<tr><td><label for="department_id"><?= $this->_(array('department', 'departments', 1)); ?></label></td>
					<td><select name="department_id" id="department_id">
							<option></option>
						<?php
							$table = new DepartmentTable();
							$list = $table->find();
							foreach ($list as $department) {
								$name = self::escape($department->getName());
								$selected = $this->person->getDepartment_id()==$department->getId()
											? 'selected="selected"'
											: '';
								echo "<option value=\"{$department->getId()}\" $selected>$name</option>";
							}
						?>
						</select>
					</td>
				</tr>
				<tr><td><label for="authenticationMethod"><?= $this->_('authenticationMethod'); ?></label></td>
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
				<tr><td><label for="username"><?= $this->_('username'); ?></label></td>
					<td><input name="username" id="username" value="<?= self::escape($this->person->getUsername()); ?>" />
					</td>
				</tr>
				<tr><td><label for="password"><?= $this->_('password'); ?></label></td>
					<td><input name="password" id="password" /></td>
				</tr>
				<tr><td><label for="role"><?= $this->_('role'); ?></label></td>
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

			<p><?= $this->_('text.employee_authentication_description'); ?></p>
		</fieldset>

		<?php include APPLICATION_HOME.'/blocks/html/people/personInfoFields.inc'; ?>

		<fieldset><legend><?= $this->_('submit'); ?></legend>
			<?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/users');
			?>
		</fieldset>
	</form>
</div>
