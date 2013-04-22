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
				echo '<a class="btn" href="'.BASE_URI.'/users/update"><span class="icon-plus"></span> Add User Account</a>';
			}
		?>
		User Accounts
	</h2>
	<table>
		<thead>
			<tr>
				<th>Username</th>
				<th>Name</th>
				<th>Department</th>
				<th>Authentication</th>
				<th>Roles</th>
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
						<span class=\"hidden-label\">Edit Account</span>
					</a>
					";
					$deleteButton = "
					<a class=\"icon-remove\"
						href=\"".BASE_URI."/users/delete?person_id={$user->getId()}\">
						<span class=\"hidden-label\">Delete Account</span>
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
