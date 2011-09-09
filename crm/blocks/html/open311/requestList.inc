<?php
/**
 * Displays a list of Tickets using Open311 terminology
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TicketList $this->ticketList
 */
echo "
<div class=\"requestList\">
	<h1>Requests</h1>
";
	if (count($this->ticketList)) {
		$page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
		$ticketList = $this->ticketList->getPaginator(50,$page);
		echo "
		<table>
			<thead>
				<tr><th>service_request_id</th>
					<th>status</th>
					<th>service_name</th>
					<th>service_code</th>
					<th>description</th>
					<th>requested_datetime</th>
					<th>updated_datetime</th>
					<th>address</th>
					<th>address_id</th>
					<th>zipcode</th>
					<th>lat</th>
					<th>long</th>
					<th>media_url</th>
				</tr>
			</thead>
			<tbody>
		";
		foreach ($ticketList as $ticket) {
			$service_name = View::escape($ticket->getData('category.name'));
			$service_code = View::escape($ticket->getData('category._id'));

			$issues = $ticket->getIssues();
			$description = $issues[0]->getDescription();

			$history = $ticket->getHistory();
			$latestHistory = end($history);

			$address = View::escape($ticket->getLocation());
			$zip = View::escape($ticket->getZip());
			$media = $issues[0]->getMedia();
			$media_url = count($media) ? $media[0]->getURL() : '';

			echo "
			<tr><td>{$ticket->getId()}</td>
				<td>{$ticket->getStatus()}</td>
				<td>$service_name</td>
				<td>$service_code</td>
				<td>$description</td>
				<td>{$ticket->getEnteredDate('c')}</td>
				<td>{$latestHistory->getActionDate('c')}</td>
				<td>$address</td>
				<td>{$ticket->getAddress_id()}</td>
				<td>{$zip}</td>
				<td>{$ticket->getLatitude()}</td>
				<td>{$ticket->getLongitude()}</td>
				<td><a href=\"$media_url\">$media_url</a></td>
			</tr>
			";
		}

		echo "
			</tbody>
		</table>
		";
	}
	else {
		echo "<p>No cases found<p>";
	}
echo "
</div>
";

if (isset($ticketList) && $ticketList instanceof Zend_Paginator) {
	$block = new Block('pageNavigation.inc',array('paginator'=>$ticketList));
	echo $block->render('html',$this->template);
}
