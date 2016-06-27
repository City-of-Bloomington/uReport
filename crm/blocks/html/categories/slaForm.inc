<?php
/**
 * @copyright 2013-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param CategoryList $this->categoryList
 */
use Blossom\Classes\View;
?>
<div class="slaForm">
	<h2><?= $this->_('service_level_agreement'); ?></h2>
	<p><?= $this->_('service_level_agreement_description', 'messages'); ?></p>

	<form method="post" action="<?= BASE_URI; ?>/categories/sla">
		<fieldset><legend><?= $this->_(array('category','categories', 2)); ?></legend>
			<table>
				<thead>
					<tr><th><?= $this->_(array('category', 'categories', 1)); ?></th>
						<th><?= $this->_(array('department', 'departments', 1)); ?></th>
						<th><?= $this->_('sla'); ?>
							<?= $this->_(array('day', 'days', 2)); ?>
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
