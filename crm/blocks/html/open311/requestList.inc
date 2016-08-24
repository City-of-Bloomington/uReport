<?php
/**
 * Displays a list of Tickets using Open311 terminology
 *
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param TicketList $this->ticketList
 */
use Blossom\Classes\Block;

echo "
<div class=\"requestList\">
	<h2>Requests</h2>
";
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
			$service_name = $c ? self::escape($c->getName()) : '';
			$service_code = $c ? $c->getId()   : '';

            $description = $t->getDescription();

            $media_url = '';
            $media = $t->getMedia();
            if (count($media)) {
                $m = $media->current();
                $media_url = $m->getURL();
            }

			$history = $t->getHistory();
			$latestHistory = end($history);

			$address = self::escape($t->getLocation());
			$zip = self::escape($t->getZip());

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
$block = new Block('pageNavigation.inc', ['paginator'=>$this->ticketList]);
echo $block->render('html',$this->template);
