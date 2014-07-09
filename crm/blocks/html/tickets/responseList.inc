<?php
/**
 * @copyright 2011-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->responses
 */
use Blossom\Classes\View;

$responseButton = '';
if (Person::isAllowed('issues','update')
	&& $this->issue->getReportedByPerson()) {
	$responseButton = "
	<a class=\"btn\"
		href=\"".BASE_URL."/issues/respond?issue_id={$this->issue->getId()}\">
		<span class=\"icon-plus\">
		</span>
		{$this->translate('labels.add_response')}
	</a>
	";
}
?>
<div class="responseList">
	<h2><?php
			echo $this->translate(array('labels.response', 'labels.responses', 2)).' '.$responseButton;
		?>
	</h2>
	<table>
		<thead>
			<tr><th><?php echo $this->translate('labels.date'); ?></th>
				<th><?php echo $this->translate(array('labels.person', 'labels.people', 1)); ?></th>
				<th><?php echo $this->translate(array('labels.contactMethod', 'labels.contactMethods', 1)); ?></th>
				<th><?php echo $this->translate(array('labels.comment', 'labels.comments', 2)); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($this->responses as $response) {
				$person        = $response->getPerson();
				$id            = $person->getId();
				$name          = View::escape($person->getFullname());
				$contactMethod = View::escape($response->getContactMethod());
				$notes         = View::escape($response->getNotes());
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
