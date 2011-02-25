<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param ActionList $this->actionList
 */
?>
<div class="interfaceBox">
	<h1><?php
			if (userIsAllowed('Actions')) {
				echo $this->template->linkButton(
					'Add',BASE_URL.'/actions/updateAction.php','add'
				);
			}
		?>
		Actions
	</h1>
	<table>
		<thead>
			<tr><th></th>
				<th>Noun</th>
				<th>Description</th>
				<th>Form Label</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($this->actionList as $action) {
				$editButton = '';
				if (userIsAllowed('Actions')) {
					$url = new URL(BASE_URL.'/actions/updateAction.php');
					$url->id = $action->getId();

					$editButton = $this->template->linkButton(
						'Edit',
						BASE_URL.'/actions/updateAction.php?action_id='.$action->getId(),
						'edit'
					);
				}
				$name = View::escape($action->getName());
				$description = View::escape($action->getDescription());
				$formLabel = View::escape($action->getFormLabel());
				$status = View::escape($action->getStatus());
				echo "
				<tr><td>$editButton</td>
					<td>$name</td>
					<td>$description</td>
					<td>$formLabel</td>
					<td>$status</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
</div>