<?php
/**
 * @copyright 2007-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param array $this->users
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
?>
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
        $helper        = $this->template->getHelper('buttonLink');

        foreach ($this->users as $user) {
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

            $department     = $user->getDepartment();
            $departmentName = $department ? self::escape($department->getName()) : '';
            $role           = $this->_(strtolower($user->getRole()));
            echo "
            <tr><td>$username</td>
                <td>$name</td>
                <td>$departmentName</td>
                <td>{$user->getAuthenticationMethod()}</td>
                <td>$role</td>
                <td>$editButton</td>
                <td>$deleteButton</td>
            </tr>
            ";
        }
    ?>
    </tbody>
</table>
