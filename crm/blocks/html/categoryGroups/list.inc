<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param CategoryGroupList $this->categoryGroupList
 */
?>
<div class="categoryGroupList">
	<h2>Groups
		<?php
			if (userIsAllowed('categoryGroups','update')) {
				echo "
				<a class=\"add button\" href=\"".BASE_URI."/categoryGroups/update\">
					Add
				</a>
				<a class=\"reorder button\" href=\"".BASE_URI."/categoryGroups/reorder\">
					Reorder
				</a>
				";
			}
		?>
	</h2>
	<table>
		<thead><tr><th></th><th>Name</th><th>Order</th><th></th></tr></thead>
		<tbody>
		<?php
			foreach ($this->categoryGroupList as $group) {
				$editButton = '';
				$deleteButton = '';
				if (userIsAllowed('categoryGroups','update')) {
					$editButton = "
					<a class=\"edit button\"
						href=\"".BASE_URI."/categoryGroups/update?categoryGroup_id={$group->getId()}\">
						Edit
					</a>
					";
					$deleteButton = "
					<a class=\"delete button\"
						href=\"".BASE_URI."/categoryGroups/delete?categoryGroup_id={$group->getId()}\">
						Delete
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