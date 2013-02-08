<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 *
 * Implements optional JSONP resonses
 *
 * If the client provides a callback (function name), the JSON response
 * is wrapped in a call to the function they requested.
 */
header('Content-type: text/json; charset=utf-8');
if (isset($_GET['callback'])) {
	echo $_GET['callback'].'(';
}
include FRAMEWORK.'/errorMessages.php';
echo $this->includeBlocks();
if (isset($_GET['callback'])) {
	echo ');';
};