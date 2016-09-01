<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param SubstatusList $this->substatusList
 */
use Application\Models\Person;
?>
<section class="substatusList">
    <header>
        <h1><?= $this->template->title; ?></h1>
        <div class="tools">
        <?php
			if (Person::isAllowed('substatus', 'update')) {
				$helper = $this->template->getHelper('buttonLink');
				echo $helper->buttonLink(
					BASE_URI.'/substatus/update',
					$this->_('add'),
					'add'
				);
			}
		?>
		</div>
    </header>
	<table>
		<?php
			foreach ($this->substatusList as $substatus) {
				$editButton = '';
				if (Person::isAllowed('substatus','update')) {
					$editButton = $helper->buttonLink(
						BASE_URI."/substatus/update?substatus_id={$substatus->getId()}",
						$this->_('edit'),
						'edit'
					);
				}
				$status      = self::escape($substatus->getStatus());
				$name        = self::escape($substatus->getName());
				$description = self::escape($substatus->getDescription());
				$default = $substatus->isDefault() ? $this->_('default') : '';
				echo "
				<tr><td>$editButton</td>
					<td>$status</td>
					<td>$name</td>
					<td>$default</td>
					<td>$description</td>
				</tr>
				";
			}
		?>
	</table>
</section>
