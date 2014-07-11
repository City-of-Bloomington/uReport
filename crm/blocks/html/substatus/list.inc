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
					$this->translate('labels.add'),
					'add'
				);
			}
			$this->translate(array('labels.substatus', 'labels.substatuses', 2));
		?>
	</h2>
	<table>
		<?php
			foreach ($this->substatusList as $substatus) {
				$editButton = '';
				if (Person::isAllowed('substatus','update')) {
					$editButton = $helper->buttonLink(
						BASE_URI."/substatus/update?substatus_id={$substatus->getId()}",
						$this->translate('labels.edit'),
						'edit'
					);
				}
				$status      = View::escape($substatus->getStatus());
				$name        = View::escape($substatus->getName());
				$description = View::escape($substatus->getDescription());
				$default = $substatus->isDefault() ? $this->translate('labels.default') : '';
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
