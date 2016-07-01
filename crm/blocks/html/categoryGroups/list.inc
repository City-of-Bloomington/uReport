<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param CategoryGroupList $this->categoryGroupList
 */
use Application\Models\Person;
?>
<section class="categoryGroupList">
    <header>
        <h1><?= $this->_('categoryGroup'); ?></h1>
        <div class="tools">
        <?php
			if (Person::isAllowed('categoryGroups','update')) {
				$helper = $this->template->getHelper('buttonLink');
				echo $helper->buttonLink(
					BASE_URI.'/categoryGroups/update',
					$this->_('add'),
					'add'
				);
				echo $helper->buttonLink(
					BASE_URI.'/categoryGroups/reorder',
					$this->_('reorder'),
					'reorder'
				);
			}
		?>
		</div>
	</h2>
	<table>
		<thead>
			<tr><th></th>
				<th><?= $this->_('name' ); ?></th>
				<th><?= $this->_('order'); ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($this->categoryGroupList as $group) {
				$editButton   = '';
				$deleteButton = '';
				if (Person::isAllowed('categoryGroups','update')) {
					$editButton = $helper->buttonLink(
						BASE_URI.'/categoryGroups/update?categoryGroup_id='.$group->getId(),
						$this->_('edit'),
						'edit'
					);
					$deleteButton = $helper->buttonLink(
						BASE_URI.'/categoryGroups/delete?categoryGroup_id='.$group->getId(),
						$this->_('delete'),
						'delete'
					);
				}
				$name  = self::escape($group->getName());
				$order = (int)$group->getOrdering();
				echo "
				<tr><td>$editButton</td>
					<td>$name</td>
					<td>$order</td>
					<td>$deleteButton</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
</section>
