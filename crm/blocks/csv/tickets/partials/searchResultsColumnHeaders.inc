<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TicketList $this->ticketList
 */
if (count($this->ticketList)) {
	$url = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	$displayableFields = TicketList::getDisplayableFields();
	$sortableFields = TicketList::getSortableFields();
	$sortingIsSupported = count($this->ticketList) < TicketList::$MAX_SORTABLE_ROWS;

	// Loop through all the fields the user asked to see
	$line = array();
	foreach ($_GET['fields'] as $name=>$value) {
		// Make sure they're allowed to see this field
		if (array_key_exists($name,$displayableFields)
			&& (false === strpos($name,'Person') || userIsAllowed('People'))) {

			$line[] = $displayableFields[$name]['displayName'];
		}
	}
	echo implode('|',$line)."\n";
}
