<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$url = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
if ($url->person_id) {
	unset($url->parameters['person_id']);
}
echo "<div class=\"changePersonButton\">";
echo $this->template->linkButton(
	'Change Person',$url,'change',null,null,'button'
);
echo "</div>";