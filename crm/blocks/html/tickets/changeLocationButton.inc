<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$return_url = new URL($_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL']);
foreach ($_GET as $key=>$value) {
	if ($key!=='location') {
		$return_url->$key = $value;
	}
}
$url = BASE_URL.'/locations?return_url='.urlencode($return_url);
echo "
<div class=\"changeLocationButton\">
	<a id=\"findAddressButton\" class=\"edit button\" href=\"$url\">
		Lookup Location
	</a>
</div>
<div id=\"find_location_overlay\"></div>
";
