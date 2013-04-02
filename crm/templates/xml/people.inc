<?xml version="1.0" encoding="UTF-8"?>
<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
header('Content-type: text/xml; charset=utf-8');
include FRAMEWORK.'/errorMessages.php';
echo $this->includeBlocks();