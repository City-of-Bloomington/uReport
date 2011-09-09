<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
if (isset($_GET['callback'])) {
	echo $_GET['callback'].'(';
}
echo $this->includeBlocks();
if (isset($_GET['callback'])) {
	echo ');';
};