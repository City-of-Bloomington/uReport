<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<div class="categoryGroupReportForm">
	<h2>Reorder Groups</h2>
	<form method="post" action="<?php echo BASE_URI; ?>/categoryGroups/reorder">
		<table>
			<thead>
				<tr><th>Name</th>
					<th>Order</th>
				</tr>
			</thead>
			<tbody>
			<?php
				$groups = new CategoryGroupList();
				$groups->find();
				foreach ($groups as $group) {
					$name = View::escape($group->getName());
					$order = (int)$group->getOrdering();
					echo "
					<tr><td><label for=\"categoryGroups_{$group->getId()}\">$name</label></td>
						<td><input name=\"categoryGroups[{$group->getId()}]\"
								id=\"categoryGroups_{$group->getId()}\"
								value=\"$order\" />
						</td>
					</tr>
					";
				}
			?>
			</tbody>
		</table>
		<button type="submit" class="submit">Submit</button>
		<a href="<?php echo BASE_URI; ?>/categoryGroups">Cancel</a>
	</form>
</div>