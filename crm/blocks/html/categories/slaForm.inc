<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param CategoryList $this->categoryList
 */
use Blossom\Classes\View;
?>
<div class="slaForm">
	<h2><?= $this->_('labels.service_level_agreement'); ?></h2>
	<p><?= $this->_('text.service_level_agreement_description'); ?></p>

	<form method="post" action="<?= BASE_URI; ?>/categories/sla">
		<fieldset><legend><?= $this->_(array('labels.category','labels.categories', 2)); ?></legend>
			<table>
				<thead>
					<tr><th><?= $this->_(array('labels.category', 'labels.categories', 1)); ?></th>
						<th><?= $this->_(array('labels.department', 'labels.departments', 1)); ?></th>
						<th><?= $this->_('labels.sla'); ?>
							<?= $this->_(array('labels.day', 'labels.days', 2)); ?>
						</th>
					</tr>
				</thead>
				<tbody>
				<?php
					foreach ($this->categoryList as $category) {
						$name = self::escape($category->getName());
						$d = $category->getDepartment();
						$department = $d ? self::escape($d->getName()) : '';

						$field = "categories[{$category->getId()}]";
						echo "
						<tr><td>$name</td>
							<td>$department</td>
							<td><input type=\"hidden\"
									name=\"{$field}[id]\"
									value=\"{$category->getId()}\" />
								<input name=\"{$field}[slaDays]\"
									size=\"2\" maxlength=\"2\"
									value=\"{$category->getSlaDays()}\" />
							</td>
						</tr>
						";
					}
				?>
				</tbody>
			</table>
			<?php
				$h = $this->template->getHelper('saveAndCancelButtons');
				echo $h->saveAndCancelButtons(BASE_URI.'/categories');
			?>
		</fieldset>
	</form>
</div>
