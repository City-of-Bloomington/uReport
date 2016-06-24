<?php
/**
 * @copyright 2011-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->responses
 */
use Application\Models\Person;
use Blossom\Classes\View;

$responseButton = '';
if (Person::isAllowed('issues','update')
	&& $this->issue->getReportedByPerson()) {
	$h = $this->template->getHelper('buttonLink');
	$responseButton = $h->buttonLink(
		BASE_URL.'/issues/respond?issue_id='.$this->issue->getId(),
		$this->_('labels.add_response'),
		'add'
	);
}
?>
<div class="responseList">
	<h2><?php
			echo $this->_(array('labels.response', 'labels.responses', 2)).' '.$responseButton;
		?>
	</h2>
	<table>
		<thead>
			<tr><th><?= $this->_('labels.date'); ?></th>
				<th><?= $this->_(array('labels.person', 'labels.people', 1)); ?></th>
				<th><?= $this->_(array('labels.contactMethod', 'labels.contactMethods', 1)); ?></th>
				<th><?= $this->_(array('labels.comment', 'labels.comments', 2)); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($this->responses as $response) {
				$person        = $response->getPerson();
				$id            = $person->getId();
				$name          = self::escape($person->getFullname());
				$contactMethod = self::escape($response->getContactMethod());
				$notes         = self::escape($response->getNotes());
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
