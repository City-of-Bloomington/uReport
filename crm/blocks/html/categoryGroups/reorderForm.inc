<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<div class="categoryGroupReportForm">
	<h2><?php
			echo $this->translate('labels.reorder').' '.
				 $this->translate(array('labels.categoryGroup', 'labels.categoryGroups', 2));
		?>
	</h2>
	<form method="post" action="<?php echo BASE_URI; ?>/categoryGroups/reorder">
		<table>
			<thead>
				<tr><th><?php echo $this->translate('labels.name' ); ?></th>
					<th><?php echo $this->translate('labels.order'); ?></th>
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
		<?php
			$helper = $this->template->getHelper('saveAndCancelButtons');
			echo $helper->saveAndCancelButtons(BASE_URI.'/categoryGroups');
		?>
	</form>
</div>
