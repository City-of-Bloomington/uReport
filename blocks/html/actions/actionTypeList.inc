<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param ActionTypeList $this->actionTypeList
 */
?>
<div class="interfaceBox">
	<h1><?php
			if (userIsAllowed('ActionTypes')) {
				echo $this->template->linkButton(
					'Add',BASE_URL.'/actions/updateActionType.php','add'
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
			foreach ($this->actionTypeList as $actionType) {
				$editButton = '';
				if (userIsAllowed('ActionTypes')) {
					$url = new URL(BASE_URL.'/actions/updateActionType.php');
					$url->id = $actionType->getId();

					$editButton = $this->template->linkButton(
						'Edit',
						BASE_URL.'/actions/updateActionType.php?actionType_id='.$actionType->getId(),
						'edit'
					);
				}
				$name = View::escape($actionType->getName());
				$description = View::escape($actionType->getDescription());
				$formLabel = View::escape($actionType->getFormLabel());
				$status = View::escape($actionType->getStatus());
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