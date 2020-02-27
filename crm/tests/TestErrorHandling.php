<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
include '../bootstrap.inc';

// Let's cause an error and see what happens
$category = new Category('XXXXX');
echo "\n";
