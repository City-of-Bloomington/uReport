<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$return_url = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
$url = BASE_URL."/people?return_url=$return_url";
echo "<div class=\"changePersonButton\">";
echo $this->template->linkButton(
	'Lookup Person',$url,'edit',null,null,'button'
);
echo "</div>";