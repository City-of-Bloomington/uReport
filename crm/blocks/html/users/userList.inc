<?php
/**
 * @copyright 2007-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param PersonList $this->userList
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
?>
<section>
    <header>
        <h1><?= $this->template->title; ?></h1>
        <div class="tools">
        <?php
			if (Person::isAllowed('users','add')) {
				$helper = $this->template->getHelper('buttonLink');
				echo $helper->buttonLink(
					BASE_URI.'/users/update',
					$this->_('create_account'),
					'add'
				);
			}
		?>
	</header>
	<table>
		<thead>
			<tr><th><?= $this->_('username'); ?></th>
				<th><?= $this->_('name'); ?></th>
				<th><?= $this->_('department'); ?></th>
				<th><?= $this->_('authenticationMethod'); ?></th>
				<th><?= $this->_('role'); ?></th>
				<th></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php
            $userCanEdit   = Person::isAllowed('users',  'edit');
            $userCanDelete = Person::isAllowed('users',  'delete');
            $userCanView   = Person::isAllowed('people', 'view');

			foreach ($this->userList as $user) {
				$editButton   = '';
				$deleteButton = '';
				if ($userCanEdit) {
					$editButton = $helper->buttonLink(
						BASE_URI.'/users/update?person_id='.$user->getId(),
						$this->_('edit'),
						'edit',
						ButtonLink::SIZE_ICON
					);
                }
                if ($userCanDelete) {
					$deleteButton = $helper->buttonLink(
						BASE_URI.'/users/delete?person_id='.$user->getId(),
						$this->_('delete'),
						'delete',
						ButtonLink::SIZE_ICON
					);
				}

				$username = self::escape($user->getUsername());
				$name     = self::escape("{$user->getFirstname()} {$user->getLastname()}");
				if ($userCanView) {
                    $uri  = BASE_URI.'/people/view?person_id='.$user->getId();
                    $name = "<a href=\"$uri\">$name</a>";
				}

				$department = $user->getDepartment();
				$departmentName = $department ? self::escape($department->getName()) : '';
				echo "
				<tr><td>$username</td>
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
</section>
