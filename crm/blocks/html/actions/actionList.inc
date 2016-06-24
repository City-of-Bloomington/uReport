<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\ActionTable;
use Application\Models\Person;

$t    = new ActionTable();
$list = $t->find();
?>
<section>
    <header>
        <h1>
        <?php
			if (Person::isAllowed('actions', 'add')) {
				$helper = $this->template->getHelper('buttonLink');
				echo $helper->buttonLink(
					BASE_URI.'/actions/update',
					$this->_('add'),
					'add'
				);
			}
			echo $this->_(['action', 'actions', count($list)]);
		?>
		</h1>
    </header>
	<table>
		<thead>
			<tr><th></th>
				<th><?= $this->_('action'); ?></th>
				<th><?= $this->_('description'); ?></th>
				<th><?= $this->_('type'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
            $userCanEdit = Person::isAllowed('actions', 'update');

			foreach ($list as $action) {
				$editButton = '';
				if ($userCanEdit) {
					$editButton = $helper->buttonLink(
						BASE_URI.'/actions/update?action_id='.$action->getId(),
						$this->_('edit'),
						'edit'
					);
				}
				$name        = self::escape($action->getName());
				$description = self::escape($action->getDescription());
				$type        = self::escape($action->getType());
				echo "
				<tr><td>$editButton</td>
					<td>$name</td>
					<td>$description</td>
					<td>$type</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
</section>
