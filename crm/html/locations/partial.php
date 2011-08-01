<?php
/**
 * Displays a single block
 *
 * The script is a mirror of /locations/home.php
 * It responds to the same requests, but also lets you specify
 * a single block to to output.
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$block = new Block($_GET['partial']);

if (isset($_GET['return_url'])) {
	$block->return_url = $_GET['return_url'];
}
if (isset($_GET['location'])) {
	$block->results = Location::search($_GET['location']);
}

$template = new Template('partial','html');
$template->blocks[] = $block;
echo $template->render();