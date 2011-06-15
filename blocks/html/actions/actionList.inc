<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<div class="interfaceBox">
	<h1><?php
			if (userIsAllowed('Actions')) {
				echo $this->template->linkButton(
					'Add',BASE_URL.'/actions/updateAction.php','add','icon'
				);
			}
		?>
		Actions
	</h1>
	<table>
		<thead>
			<tr><th></th>
				<th>Action</th>
				<th>Description</th>
				<th>Type</th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach (ActionList::getActions() as $action) {
				$editButton = '';
				if (userIsAllowed('Actions')) {
					$editButton = $this->template->linkButton(
						'Edit',
						BASE_URL."/actions/updateAction.php?action_id={$action->getId()}",
						'edit'
					);
				}
				$name = View::escape($action->getName());
				$description = View::escape($action->getDescription());
				$type = View::escape($action->getType());
				echo "
				<tr><td>$editButton</td>
					<td>$name</td>
					<td>$description</td>
					<td>$type</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
</div>