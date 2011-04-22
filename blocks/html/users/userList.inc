<?php
/**
 * @copyright 2007-2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param PersonList $this->userList
 */
?>
<div class="interfaceBox">
	<h1><?php
			if (userIsAllowed('Users')) {
				echo $this->linkButton(
					'Add User Account',BASE_URL.'/users/updateUser.php','add'
				);
			}
		?>
		User Accounts
	</h1>
	<table>
		<thead>
			<tr><th></th>
				<th>Username</th>
				<th>Name</th>
				<th>Department</th>
				<th>Authentication</th>
				<th>Roles</th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($this->userList as $user) {
				$editButton = '';
				$deleteButton = '';
				if (userIsAllowed('Users')) {
					$editButton = $this->linkButton(
						'Edit User Account',
						BASE_URL.'/users/updateUser.php?user_id='.$user->getId(),
						'edit'
					);

					$deleteButton = $this->linkButton(
						'Delete User Account',
						BASE_URL.'/users/deleteUser.php?user_id='.$user->getId(),
						'delete'
					);
				}

				$username = View::escape($user->getUsername());
				$name = View::escape("{$user->getFirstname()} {$user->getLastname()}");
				$email = View::escape($user->getEmail());

				$department = $user->getDepartment();
				$departmentName = View::escape($department['name']);
				echo "
				<tr><td>$editButton</td>
					<td>$username</td>
					<td>$name</td>
					<td>$departmentName</td>
					<td>{$user->getAuthenticationMethod()}</td>
					<td>
				";
						foreach ($user->getRoles() as $role) {
							echo "$role ";
						}
				echo "
					</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
</div>
