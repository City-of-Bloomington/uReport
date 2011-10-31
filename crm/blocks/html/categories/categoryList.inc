<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param CategoryList $this->categoryList
 */
?>
<div class="categoryList">
	<h1>Categories
		<?php
			if (userIsAllowed('Categories')) {
				echo $this->template->linkButton(
					'Add',BASE_URL.'/categories/updateCategory.php','add'
				);
			}
		?>
	</h1>
	<?php
		$groups = array();
		$ids = array();
		foreach ($this->categoryList as $category) {
			$group = $category->getGroup();
			if (!$group) {
				$group = 'other';
			}
			$groups[$group]['categories'][] = $category;
			if (empty($groups[$group]['id'])) {
				$groups[$group]['id'] = preg_replace('/[^a-zA-Z]/','',$group);
			}

		}
		ksort($groups);

		echo "<div class=\"tableOfContents\"><ul>";
		foreach ($groups as $group) {
			echo "<li><a href=\"#$group[id]\">$group[id]</a></li>";
		}
		echo "</ul></div>";

		foreach ($groups as $groupName=>$group) {
			$groupName = View::escape($groupName);
			echo "
			<div class=\"group\">
				<h2><a id=\"$group[id]\">$groupName</a></h2>
			";
			foreach ($group['categories'] as $category) {
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
				<div class=\"category\">
					<h3>$name $editButton</h3>
					<p>$description</p>
					<table>
						<thead>
							<tr><th>Department</th>
								<th>Posting</th>
								<th>Display</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>$department</td>
								<td>$postPermission</td>
								<td>$displayPermission</td>
							</tr>
						</tbody>
					</table>
				</div>
				";
			}
		echo "
		</div>
		";
		}
	?>
</div>
