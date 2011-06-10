<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$return_url = new URL($_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']);
foreach ($_GET as $key=>$value) {
	if ($key!=='location') {
		$return_url->$key = $value;
	}
}
$url = BASE_URL."/locations?return_url=$return_url";
echo "<div class=\"changeLocationButton\">";
echo $this->template->linkButton(
	'Find an Address',$url,'change','findAddressButton',null,'button'
);
echo "
</div>
<div id=\"find_location_overlay\"></div>
";
