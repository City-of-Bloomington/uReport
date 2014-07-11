<?php
/**
 * @copyright 2007-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param PersonList $this->userList
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;

use Blossom\Classes\View;
?>
<div class="interfaceBox">
	<h2><?php
			if (Person::isAllowed('users','add')) {
				$helper = $this->template->getHelper('buttonLink');
				echo $helper->buttonLink(
					BASE_URI.'/users/update',
					$this->translate('labels.create_account'),
					'add'
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
				$editButton   = '';
				$deleteButton = '';
				if (Person::isAllowed('users','update')) {
					$editButton = $helper->buttonLink(
						BASE_URI.'/users/update?person_id='.$user->getId(),
						$this->translate('labels.edit'),
						'edit',
						ButtonLink::SIZE_ICON
					);
					$deleteButton = $helper->buttonLink(
						BASE_URI.'/users/delete?person_id='.$user->getId(),
						$this->translate('labels.delete'),
						'delete',
						ButtonLink::SIZE_ICON
					);
				}

				$username = View::escape($user->getUsername());
				$name     = View::escape("{$user->getFirstname()} {$user->getLastname()}");

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
