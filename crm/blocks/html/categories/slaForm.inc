<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param CategoryList $this->categoryList
 */
?>
<div class="slaForm">
	<h2><?php echo $this->translate('labels.service_level_agreement'); ?></h2>
	<p><?php echo $this->translate('text.service_level_agreement_description'); ?></p>

	<form method="post" action="<?php echo BASE_URI; ?>/categories/sla">
		<fieldset><legend><?php echo $this->translate(array('labels.category','labels.categories', 2)); ?></legend>
			<table>
				<thead>
					<tr><th><?php echo $this->translate(array('labels.category', 'labels.categories', 1)); ?></th>
						<th><?php echo $this->translate(array('labels.department', 'labels.departments', 1)); ?></th>
						<th><?php echo $this->translate('labels.sla'); ?>
							<?php echo $this->translate(array('labels.day', 'labels.days', 2)); ?>
						</th>
					</tr>
				</thead>
				<tbody>
				<?php
					foreach ($this->categoryList as $category) {
						$name = View::escape($category->getName());
						$d = $category->getDepartment();
						$department = $d ? View::escape($d->getName()) : '';

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

			<button type="submit"><span class="icon-save"></span> <?php echo $this->translate('labels.save'); ?></button>
			<a class="btn" href="<?php echo BASE_URI; ?>/categories">
				<span class="icon-remove"></span>
				<?php echo $this->translate('labels.cancel'); ?>
			</a>
		</fieldset>
	</form>
</div>
