<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Department $this->department
 */
?>
<div class="interfaceBox">
	<h1><?php echo View::escape($this->department->getName()); ?> Actions</h1>
	<table>
		<thead>
			<tr><th></th>
				<th>Action</th>
				<th>Description</th>
				<th>Form Label</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php
			$url = new URL(BASE_URL.'/departments/updateAction.php');
			$url->department_id = "{$this->department->getId()}";

			foreach ($this->department->getActions() as $index=>$action) {
				$editButton = '';
				if (userIsAllowed('Departments')) {
					$url->index = $index;
					$editButton = $this->template->linkButton('Edit',$url,'edit');

					$deleteButton = $this->template->linkButton(
						'Delete',
						BASE_URL."/departments/deleteAction.php?department_id={$this->department->getId()};index=$index",
						'delete'
					);
				}
				$name = isset($action['name']) ? View::escape($action['name']) : '';
				$description = isset($action['description']) ? View::escape($action['description']) : '';
				$formLabel = isset($action['formLabel']) ? View::escape($action['formLabel']) : '';
				echo "
				<tr><td>$editButton</td>
					<td>$name</td>
					<td>$description</td>
					<td>$formLabel</td>
					<td>$deleteButton</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
	<?php
		if (userIsAllowed('Departments')) {
			$url->index = null;
			echo $this->template->linkButton('Add an action',$url,'add');
		}
	?>
</div>
