<?php
/**
 * @copyright 2011-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param SubstatusList $this->substatusList
 */
use Application\Models\Person;
use Blossom\Classes\View;
?>
<div class="substatusList">
	<h2><?php
			if (Person::isAllowed('substatus','update')) {
				$helper = $this->template->getHelper('buttonLink');
				echo $helper->buttonLink(
					BASE_URI.'/substatus/update',
					$this->_('add'),
					'add'
				);
			}
			$this->_(array('substatus', 'substatuses', 2));
		?>
	</h2>
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
</div>
