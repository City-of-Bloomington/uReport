<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
header('Content-type: text/json; charset=utf-8');
include BLOSSOM.'/errorMessages.php';
echo $this->includeBlocks();
