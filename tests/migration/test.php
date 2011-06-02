<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include '../../configuration.inc';

// Every person should have at least one ticket
$personList = new PersonList();
$personList->find();
foreach ($personList as $person) {
	if (!$person->hasTickets()) {
		echo "{$person->getFullname()}\n";
	}
}