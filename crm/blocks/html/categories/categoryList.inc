<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param CategoryList $this->categoryList
 */
?>
<div class="categoryList">
	<h2>Categories
		<?php
			if (userIsAllowed('categories','update')) {
				echo '<a class="add button" href="'.BASE_URI.'/categories/update">Add</a>';
			}
		?>
	</h2>
	<?php
		$toc = '<div class="tableOfContents"><ul>';
		$html = '';
		$previous = null;
		foreach ($this->categoryList as $category) {
			$group = $category->getGroup();

			if ($group && "{$group->getId()}"!=$previous) {
				$anchor = preg_replace('[^a-zA-Z]','',$group->getName());
				$name = View::escape($group->getName());

				if ($previous) {
					$html.= '</div>';
				}
				$html.= "<div class=\"group\"><h3><a id=\"$anchor\">$name</a></h3>";
				$toc.= "<li><a href=\"".BASE_URI."/categories#$anchor\">$name</a></li>";

				$previous = "{$group->getId()}";
			}

			$editButton = '';
			if (userIsAllowed('categories','update')) {
				$editButton = "
				<a class=\"edit button\"
					href=\"".BASE_URI."/categories/update?category_id={$category->getId()}\">
					Edit
				</a>
				";
			}
			$name = View::escape($category->getName());
			$department = View::escape($category->getData('department.name'));
			$postPermission = View::escape($category->getPostingPermissionLevel());
			$displayPermission = View::escape($category->getDisplayPermissionLevel());
			$description = View::escape($category->getDescription());
			$html.= "
			<div class=\"category\">
				<h4>$name $editButton</h4>
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
		echo $toc;
		echo $html;
		echo '</div>'; // Close out the last Group div
	?>
</div>
