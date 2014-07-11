<?php
/**
 * @copyright 2012-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\ActionTable;
use Application\Models\Person;
use Blossom\Classes\View;
?>
<div class="interfaceBox">
	<h2><?php
			if (Person::isAllowed('actions','update')) {
				$helper = $this->template->getHelper('buttonLink');
				echo $helper->buttonLink(
					BASE_URI.'/actions/update',
					$this->translate('labels.add'),
					'add'
				);
			}
		?>
		Actions
	</h2>
	<table>
		<thead>
			<tr><th></th>
				<th><?php echo $this->translate(array('labels.action', 'labels.actions', 1)); ?></th>
				<th><?php echo $this->translate('labels.description'); ?></th>
				<th><?php echo $this->translate('labels.type'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
			$t = new ActionTable();
			$list = $t->find();
			foreach ($list as $action) {
				$editButton = '';
				if (Person::isAllowed('actions','update') && $action->getType()!='system') {
					$editButton = $helper->buttonLink(
						BASE_URI.'/actions/update?action_id='.$action->getId(),
						$this->translate('labels.edit'),
						'edit'
					);
				}
				$name        = View::escape($action->getName());
				$description = View::escape($action->getDescription());
				$type        = View::escape($action->getType());
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
