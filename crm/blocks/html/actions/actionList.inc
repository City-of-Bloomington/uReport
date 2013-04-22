<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<div class="interfaceBox">
	<h2><?php
			if (userIsAllowed('actions','update')) {
				echo '<a class="btn" href="'.BASE_URL.'/actions/update"><span class="icon-plus"></span> Add</a>';
			}
		?>
		Actions
	</h2>
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
			$list = new ActionList();
			$list->find();
			foreach ($list as $action) {
				$editButton = '';
				if (userIsAllowed('actions','update') && $action->getType()!='system') {
					$editButton = "
					<a class=\"btn\"
						href=\"".BASE_URL."/actions/update?action_id={$action->getId()}\">
						<span class=\"icon-pencil\"></span>
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