<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$return_url = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
$url = BASE_URL."/locations?return_url=$return_url";
echo "
<div class=\"changeLocationButton\">
	<a id=\"findAddressButton\" class=\"edit button\" href=\"$url\">
		Lookup Location
	</a>
</div>
";
