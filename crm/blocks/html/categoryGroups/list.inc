<?php
/**
 * @copyright 2012-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param CategoryGroupList $this->categoryGroupList
 */
use Application\Models\Person;
use Blossom\Classes\View;
?>
<div class="categoryGroupList">
	<h2><?php
			echo $this->_(array('categoryGroup', 'categoryGroups', 2));
			if (Person::isAllowed('categoryGroups','update')) {
				$helper = $this->template->getHelper('buttonLink');
				echo $helper->buttonLink(
					BASE_URI.'/categoryGroups/update',
					$this->_('add'),
					'add'
				) .  $helper->buttonLink(
					BASE_URI.'/categoryGroups/reorder',
					$this->_('reorder'),
					'reorder'
				);
			}
		?>
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
				$editButton = '';
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
				$name = self::escape($group->getName());
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
</div>
