<?php
/**
 * @copyright 2007-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param PersonList $this->userList
 */
?>
<div class="interfaceBox">
	<h2><?php
			if (userIsAllowed('users','add')) {
				echo sprintf('<a class="btn" href="%s"><span class="icon-plus"></span> %s</a>',
					BASE_URI.'/users/update',
					$this->translate('labels.create_account')
				);
			}
			$this->translate(array('labels.user', 'labels.users', 2));
		?>
	</h2>
	<table>
		<thead>
			<tr>
				<th><?php echo $this->translate('labels.username'); ?></th>
				<th><?php echo $this->translate('labels.name'); ?></th>
				<th><?php echo $this->translate(array('labels.department', 'labels.departments', 1)); ?></th>
				<th><?php echo $this->translate('labels.authenticationMethod'); ?></th>
				<th><?php echo $this->translate('labels.role'); ?></th>
				<th></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($this->userList as $user) {
				$editButton = '';
				$deleteButton = '';
				if (userIsAllowed('users','update')) {
					$editButton = "
					<a class=\"icon-pencil\"
						href=\"".BASE_URI."/users/update?person_id={$user->getId()}\">
						<span class=\"hidden-label\">{$this->translate('labels.edit')}</span>
					</a>
					";
					$deleteButton = "
					<a class=\"icon-remove\"
						href=\"".BASE_URI."/users/delete?person_id={$user->getId()}\">
						<span class=\"hidden-label\">{$this->translate('labels.delete')}</span>
					</a>
					";
				}

				$username = View::escape($user->getUsername());
				$name = View::escape("{$user->getFirstname()} {$user->getLastname()}");

				$department = $user->getDepartment();
				$departmentName = $department ? View::escape($department->getName()) : '';
				echo "
				<tr>
					<td>$username</td>
					<td>$name</td>
					<td>$departmentName</td>
					<td>{$user->getAuthenticationMethod()}</td>
					<td>{$user->getRole()}</td>
					<td>$editButton</td>
					<td>$deleteButton</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
</div>
