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
				echo '<a class="add button" href="'.BASE_URL.'/actions/updateAction.php">Add</a>';
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
					$editButton = "
					<a class=\"edit button\"
						href=\"".BASE_URL."/actions/updateAction.php?action_id={$action->getId()}\">
						Edit
					</a>
					";
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