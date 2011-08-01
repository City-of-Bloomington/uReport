<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
// Grab the format from the file extension used in the url
$format = preg_match("/[^.]+$/",$_SERVER['REQUEST_URI'],$matches)
	? strtolower($matches[0])
	: 'html';

$template = new Template('open311',$format);
$template->blocks[] = new Block('open311/discovery.inc');
echo $template->render();