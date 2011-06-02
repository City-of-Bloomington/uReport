<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
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
				$person_id = $response->getPersonData('person','_id');
				$fullname = View::escape($response->getPersonData('person','fullname'));
				$contactMethod = View::escape($response->getContactMethod());
				$notes = View::escape($response->getNotes());
				echo "
				<tr><td>{$response->getDate(DATE_FORMAT)}</td>
					<td><a href=\"".BASE_URL."/people/viewPerson.php?person_id=$person_id\">
							$fullname
						</a>
					</th>
					<td>$contactMethod</td>
					<td>$notes</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
</div>
