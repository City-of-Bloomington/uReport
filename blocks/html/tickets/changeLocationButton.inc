<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$url = new URL($_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']);
foreach ($_GET as $key=>$value) {
	if ($key!=='location') {
		$url->$key = $value;
	}
}
echo "<div class=\"changeLocationButton\">";
echo $this->template->linkButton(
	'Change Location',$url,'change',null,null,'button'
);
echo "</div>";