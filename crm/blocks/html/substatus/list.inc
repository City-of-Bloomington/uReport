<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param SubstatusList $this->substatusList
 */
?>
<div class="substatusList">
	<h1><?php
			if (userIsAllowed('substatus','update')) {
				echo '<a class="add button" href="'.BASE_URL.'/substatus/update">Add</a>';
			}
		?>
		Substatus
	</h1>
	<table>
		<?php
			foreach ($this->substatusList as $substatus) {
				$editButton = '';
				if (userIsAllowed('substatus','update')) {
					$editButton = "
					<a class=\"edit button\"
						href=\"".BASE_URL."/substatus/update?substatus_id={$substatus->getId()}\">
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