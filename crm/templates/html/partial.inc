<?php
/**
 * Renders one or more blocks or panels
 *
 * Templates are organized into blocks and panels.
 * Panels contain multiple blocks.
 *
 * This template lets the client ask for partial content from the page.
 * This is used mostly for AJAX requests, ie. to update
 * a portion of the screen with new content, using the
 * server-side markup.
 *
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST partial A comma-separated list of blocks or panels
 */
include BLOSSOM.'/errorMessages.php';
if (false !== strpos($_REQUEST['partial'], ',')) {
	foreach (explode(',', $_REQUEST['partial']) as $partial) {
		$partial = trim($partial);
		echo $this->includeBlocks($partial);
	}
}
else {
	$partial = trim($_REQUEST['partial']);
	echo $this->includeBlocks($partial);
}
