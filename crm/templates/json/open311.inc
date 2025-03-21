<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
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
$this->_include('partials/errorMessages.inc');
echo $this->includeBlocks();
if (isset($_GET['callback'])) {
	echo ');';
};
