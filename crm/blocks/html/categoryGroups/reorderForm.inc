<?php
/**
 * @copyright 2012-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\CategoryGroupTable;
use Blossom\Classes\View;
?>
<div class="categoryGroupReportForm">
	<h2><?php
			echo $this->_('labels.reorder').' '.
				 $this->_(array('labels.categoryGroup', 'labels.categoryGroups', 2));
		?>
	</h2>
	<form method="post" action="<?= BASE_URI; ?>/categoryGroups/reorder">
		<table>
			<thead>
				<tr><th><?= $this->_('labels.name' ); ?></th>
					<th><?= $this->_('labels.order'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
				$t = new CategoryGroupTable();
				$groups = $t->find();
				foreach ($groups as $group) {
					$name = self::escape($group->getName());
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
