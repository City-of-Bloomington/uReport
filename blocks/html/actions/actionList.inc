<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param ActionList $this->actionList
 */
?>
<div class="actionList">
	<h1>History</h1>
	<table>
		<thead>
			<tr><th>Date</th>
				<th>Action</th>
				<th>Comments</th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($this->actionList as $action) {
				$person = $action->getTargetPerson() ? $action->getTargetPerson() : $action->getPerson();
				$name = View::escape($person->getFullname());
				$verb = View::escape($action->getVerb());
				$notes = View::escape($action->getNotes());

				echo "
				<tr><td>{$action->getDate('Y-m-d')}</td>
					<td>$verb $name</td>
					<td>$notes</td>
				</tr>
				";

			}
		?>
		</tbody>
	</table>
</div>