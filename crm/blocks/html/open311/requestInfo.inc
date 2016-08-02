<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Ticket $this->ticket
 */

$status = strtolower($this->ticket->getStatus());

$c = $this->ticket->getCategory();
$service_name = $c ? self::escape($c->getName()) : '';
$service_code = $c ? $c->getId() : '';

$description = self::escape($this->ticket->getDescription());
$address     = self::escape($this->ticket->getLocation());
$zipcode     = self::escape($this->ticket->getZip());

$media_url = '';
$media = $this->ticket->getMedia();
if (count($media)) {
	$m = $media[0];
	$media_url = $m->getURL();
}
$image = $media_url ? "<img src=\"$media_url\" />" : '';

echo "
<section>
    <header>
        <h1>$service_name</h1>
        <h2>service_request #{$this->ticket->getId()}</h2>
    </header>
    <p>$description</p>

    <table>
        <tr><th>requested_datetime</th>
            <td>{$this->ticket->getEnteredDate(DATE_FORMAT)}</td>
        </tr>
        <tr><th>status</th>
            <td>$status</td>
        </tr>
        <tr><th>address</th>
            <td>$address</td>
        </tr>
        <tr><th>zipcode</th>
            <td>$zipcode</td>
        </tr>
        <tr><th>lat</th>
            <td>{$this->ticket->getLatitude()}</td>
        </tr>
        <tr><th>long</th>
            <td>{$this->ticket->getLongitude()}</td>
        </tr>
        <tr><th>address_id</th>
            <td>{$this->ticket->getAddressId()}</td>
        </tr>
    </table>

    <div>$image</div>
</section>
";
