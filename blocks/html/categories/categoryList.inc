<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
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
				<th>Problems</th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($this->categoryList as $category) {
				$editButton = '';
				if (userIsAllowed('Categories')) {
					$url = new URL(BASE_URL.'/categories/updateCategory.php');
					$url->id = $category->getId();

					$editButton = $this->template->linkButton(
						'Edit',
						BASE_URL.'/categories/updateCategory.php?category_id='.$category->getId(),
						'edit'
					);
				}
				$name = View::escape($category->getName());
				$problems = count($category->getProblems());
				echo "
				<tr><td>$editButton</td>
					<td>$name</td>
					<td><a href=\"".BASE_URL."/categories/problems.php?category_id={$category->getId()}\">
							$problems
						</a>
					</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
</div>
