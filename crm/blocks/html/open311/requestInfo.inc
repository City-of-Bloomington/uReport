<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 */
$status = strtolower($this->ticket->getStatus());
$service_name = View::escape($this->ticket->getData('category.name'));
$service_code = $this->ticket->getData('category._id');

$issue = $this->ticket->getIssue();
$description = View::escape($issue->getDescription());

$address = View::escape($this->ticket->getLocation());
$zipcode = View::escape($this->ticket->getZip());

$media_url = '';
$media = $issue->getMedia();
if (count($media)) {
	$m = $media[0];
	$media_url = $m->getURL();
}
$image = $media_url ? "<img src=\"$media_url\" />" : '';

echo "
<h1>$service_name</h1>
<h2>Case #{$this->ticket->getNumber()}</h2>
<p>$description</p>

<table>
	<tr><th>Entered Date:</th>
		<td>{$this->ticket->getEnteredDate(DATE_FORMAT)}</td>
	</tr>
	<tr><th>Status:</th>
		<td>$status</td>
	</tr>
	<tr><th>Address:</th>
		<td>$address</td>
	</tr>
	<tr><th>Zip:</th>
		<td>$zipcode</td>
	</tr>
	<tr><th>Lat:</th>
		<td>{$this->ticket->getLatitude()}</td>
	</tr>
	<tr><th>Long:</th>
		<td>{$this->ticket->getLongitude()}</td>
	</tr>
	<tr><th>address_id:</th>
		<td>{$this->ticket->getAddress_id()}</td>
	</tr>
</table>

<div>$image</div>
";