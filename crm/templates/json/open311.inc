<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 *
 * Implements optional JSONP resonses
 *
 * If the client provides a callback (function name), the JSON response
 * is wrapped in a call to the function they requested.
 */
header('Content-type: application/json; charset=utf-8');
if (isset($_GET['callback'])) {
	echo $_GET['callback'].'(';
}
include BLOSSOM.'/errorMessages.php';
echo $this->includeBlocks();
if (isset($_GET['callback'])) {
	echo ');';
};
