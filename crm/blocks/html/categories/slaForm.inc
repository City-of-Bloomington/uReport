<?php
/**
 * @copyright 2013-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param CategoryList $this->categoryList
 */
?>
<section class="slaForm">
    <header>
        <h1><?= $this->_('service_level_agreement'); ?></h1>
        <p><?= $this->_('service_level_agreement_description', 'messages'); ?></p>
    </header>

	<form method="post" action="<?= BASE_URI; ?>/categories/sla">
		<fieldset>
            <legend><?= $this->_(['category','categories', count($this->categoryList)]); ?></legend>
			<table>
				<thead>
					<tr><th><?= $this->_('category'); ?></th>
						<th><?= $this->_('department'); ?></th>
						<th><?= $this->_('sla'); ?>
							<?= $this->_(['day', 'days', 2]); ?>
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
									type=\"number\"
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
</section>
