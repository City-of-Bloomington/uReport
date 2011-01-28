<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
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
				<th>Type</th>
				<th>Notes</th>
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
				$type = View::escape($category->getName());
				$notes = count($category->getNotes());
				echo "
				<tr><td>$editButton</td>
					<td>$type</td>
					<td><a href=\"".BASE_URL."/categories/notes.php?category_id={$category->getId()}\">
							$notes
						</a>
					</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
</div>