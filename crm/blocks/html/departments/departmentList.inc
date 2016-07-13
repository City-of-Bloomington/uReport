<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param DepartmentList $this->departmentList
 */
use Application\Models\Person;
?>
<section class="departmentList">
    <header>
        <h1><?= $this->_(['department', 'departments', count($this->departmentList)]); ?></h1>
        <div class="tools">
        <?php
			if (Person::isAllowed('departments','update')) {
				$helper = $this->template->getHelper('buttonLink');
				echo $helper->buttonLink(
					BASE_URI.'/departments/update',
					$this->_('add'),
					'add'
				);
			}
		?>
        </div>
    </header>
    <table>
        <thead>
            <tr><th><?= $this->_('department'); ?></th>
                <th><?= $this->_('defaultPerson'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
            foreach ($this->departmentList as $d) {
                $uri = BASE_URI."/departments/view?department_id={$d->getId()}";
                $name = self::escape($d->getName());

                $defaultPerson = $d->getDefaultPerson();
                if ($defaultPerson) {
                    $defaultPerson = self::escape($defaultPerson->getFullname());
                }

                echo "
                <tr><td><a href=\"$uri\">$name</a></td>
                    <td>$defaultPerson</td>
                </tr>
                ";
            }
        ?>
        </tbody>
	</table>
</section>
