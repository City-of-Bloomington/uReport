<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param CategoryGroupList $this->categoryGroupList
 */
?>
<div class="categoryGroupList">
	<h2><?php
			echo $this->translate(array('labels.categoryGroup', 'labels.categoryGroups', 2));
			if (Person::isAllowed('categoryGroups','update')) {
				echo "
				<a class=\"btn\" href=\"".BASE_URI."/categoryGroups/update\">
					<span class=\"icon-plus\"></span>
					{$this->translate('labels.add')}
				</a>
				<a class=\"btn\" href=\"".BASE_URI."/categoryGroups/reorder\">
					<span class=\"icon-reorder\"></span>
					{$this->translate('labels.reorder')}
				</a>
				";
			}
		?>
	</h2>
	<table>
		<thead>
			<tr><th></th>
				<th><?php echo $this->translate('labels.name' ); ?></th>
				<th><?php echo $this->translate('labels.order'); ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($this->categoryGroupList as $group) {
				$editButton = '';
				$deleteButton = '';
				if (Person::isAllowed('categoryGroups','update')) {
					$editButton = "
					<a class=\"btn\"
						href=\"".BASE_URI."/categoryGroups/update?categoryGroup_id={$group->getId()}\">
						<span class=\"icon-pencil\"></span>
						{$this->translate('labels.edit')}
					</a>
					";
					$deleteButton = "
					<a class=\"btn\"
						href=\"".BASE_URI."/categoryGroups/delete?categoryGroup_id={$group->getId()}\">
						<span class=\"icon-remove\"></span>
						{$this->translate('labels.delete')}
					</a>
					";
				}
				$name = View::escape($group->getName());
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
