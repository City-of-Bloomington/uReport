<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param SubstatusList $this->substatusList
 */
$helper = $this->template->getHelper('button');
?>
<div class="substatusList">
	<h2><?php
			if (userIsAllowed('substatus','update')) {
				echo "
				<a class=\"btn\" href=\"".BASE_URI."/substatus/update\">
					<span class=\"icon-plus\"></span>
					Add
				</a>
				";
			}
		?>
		Substatus
	</h2>
	<table>
		<?php
			foreach ($this->substatusList as $substatus) {
				$editButton = '';
				if (userIsAllowed('substatus','update')) {
					$editButton = "
					<a class=\"btn\" href=\"".BASE_URI."/substatus/update?substatus_id={$substatus->getId()}\">
						<span class=\"icon-pencil\"></span>
						Edit
					</a>
					";
				}
				$status      = View::escape($substatus->getStatus());
				$name        = View::escape($substatus->getName());
				$description = View::escape($substatus->getDescription());
				echo "
				<tr><td>$editButton</td>
					<td>$status</td>
					<td>$name</td>
					<td>$description</td>
				</tr>
				";
			}
		?>
	</table>
</div>