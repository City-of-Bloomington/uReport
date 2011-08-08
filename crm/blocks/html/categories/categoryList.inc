<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param CategoryList $this->categoryList
 */
?>
<div class="interfaceBox">
	<h1><?php
			if (userIsAllowed('Categories')) {
				echo $this->template->linkButton(
					'Add',BASE_URL.'/categories/updateCategory.php','add'
				);
			}
		?>
		Categories
	</h1>
	<table>
		<thead>
			<tr><th></th>
				<th>Name</th>
				<th>Department</th>
				<th>Post Permission</th>
				<th>Display Permission</th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($this->categoryList as $category) {
				$editButton = '';
				if (userIsAllowed('Categories')) {
					$editButton = $this->template->linkButton(
						'Edit',
						BASE_URL."/categories/updateCategory.php?category_id={$category->getId()}",
						'edit'
					);
				}
				$name = View::escape($category->getName());
				$department = View::escape($category->getData('department.name'));
				$postPermission = View::escape($category->getPostingPermissionLevel());
				$displayPermission = View::escape($category->getDisplayPermissionLevel());
				$description = View::escape($category->getDescription());
				echo "
				<tr><td>$editButton</td>
					<td>$name</td>
					<td>$department</td>
					<td>$postPermission</td>
					<td>$displayPermission</td>
				</tr>
				<tr><td colspan=\"5\">$description</td></tr>
				";
			}
		?>
		</tbody>
	</table>
</div>
