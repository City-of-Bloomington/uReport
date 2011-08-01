<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->responses
 */
?>
<div class="responseList">
	<h3>Responses</h3>
	<table>
		<thead>
			<tr><th>Date</th>
				<th>Person</th>
				<th>Contact Method</th>
				<th>Notes</th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($this->responses as $response) {
				$person = $response->getPerson();
				$id = $person->getId();
				$name = View::escape($person->getFullname());
				$contactMethod = View::escape($response->getContactMethod());
				$notes = View::escape($response->getNotes());
				echo "
				<tr><td>{$response->getDate(DATE_FORMAT)}</td>
					<td><a href=\"".BASE_URL."/people/viewPerson.php?person_id=$id\">$name</a></td>
					<td>$contactMethod</td>
					<td>$notes</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
</div>
