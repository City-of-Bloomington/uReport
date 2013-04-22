<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->responses
 */
$responseButton = '';
if (!$this->disableButtons && userIsAllowed('issues','update')) {
	if ($this->issue->getReportedByPerson()) {
			$responseButton = "
			<a class=\"btn\"
				href=\"".BASE_URL."/issues/respond?issue_id={$this->issue->getId()}\">
				<span class=\"icon-plus\">
				</span>
				Add Response
			</a>
			";
		}
}
?>
<div class="responseList">
	<h2>Responses <?php echo $responseButton; ?></h2>
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
					<td><a href=\"".BASE_URL."/people/view?person_id=$id\">$name</a></td>
					<td>$contactMethod</td>
					<td>$notes</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
</div>
