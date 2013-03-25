<?php
/**
 * Displays a list of Tickets using Open311 terminology
 *
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TicketList $this->ticketList
 */
echo "
<div class=\"requestList\">
	<h1>Requests</h1>
";
	// Pagination pages are one-based and will treat page=0
	// as exactly the same as page=1
	$page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
	$this->ticketList->setPagination(50, $page);
	if (count($this->ticketList)) {
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

		foreach ($this->ticketList as $t) {
			$c = $t->getCategory();
			$service_name = $c ? View::escape($c->getName()) : '';
			$service_code = $c ? $c->getId()   : '';

			$issue = $t->getIssue();
			$description = $issue->getDescription();

			$history = $t->getHistory();
			$latestHistory = end($history);

			$address = View::escape($t->getLocation());
			$zip = View::escape($t->getZip());
			$media = $issue->getMedia();
			$media_url = count($media) ? $media[0]->getURL() : '';

			echo "
			<tr><td>{$t->getId()}</td>
				<td>{$t->getStatus()}</td>
				<td>$service_name</td>
				<td>$service_code</td>
				<td>$description</td>
				<td>{$t->getEnteredDate('c')}</td>
				<td>{$latestHistory->getActionDate('c')}</td>
				<td>$address</td>
				<td>{$t->getAddressId()}</td>
				<td>{$zip}</td>
				<td>{$t->getLatitude()}</td>
				<td>{$t->getLongitude()}</td>
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
$block = new Block('pageNavigation.inc',array('paginator'=>$this->ticketList->getPaginator()));
echo $block->render('html',$this->template);
