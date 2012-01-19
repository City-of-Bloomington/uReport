<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$return_url = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
$url = BASE_URL."/people?return_url=$return_url";
echo "
<div class=\"changePersonButton\">
	<a id=\"findPersonButton\" class=\"edit button\" href=\"$url\">
		Lookup Person
	</a>
</div>
<div id=\"find_person_overlay\"></div>
";