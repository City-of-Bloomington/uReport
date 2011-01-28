<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$template = new Template();

$block = new Block('locations/locationPanel.inc');
if (isset($_GET['location_query'])) {
	$block->results = Location::search($_GET['location_query']);
}
$template->blocks[] = $block;

echo $template->render();