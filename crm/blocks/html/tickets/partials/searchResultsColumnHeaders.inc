<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TicketList $this->ticketList
 */
echo "
<thead>
	<tr>
";
	if (count($this->ticketList)) {
		if (isset($_GET['sort'])) {
			$keys = array_keys($_GET['sort']);
			$currentSortField = $keys[0];
			$currentSortDirection = $_GET['sort'][$currentSortField]==1 ? 'ascending' : 'descending';
		}
		else {
			$currentSortField = 'enteredDate';
			$currentSortDirection = 'descending';
		}

		$url = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
		$displayableFields = TicketList::getDisplayableFields();
		$sortableFields = TicketList::getSortableFields();
		$sortingIsSupported = count($this->ticketList) < TicketList::$MAX_SORTABLE_ROWS;

		// Loop through all the fields the user asked to see
		foreach ($_GET['fields'] as $name=>$value) {
			// Make sure they're allowed to see this field
			if (array_key_exists($name,$displayableFields)
				&& (false === strpos($name,'Person') || userIsAllowed('People'))) {

				$label = $displayableFields[$name]['displayName'];

				// Draw any sorting buttons we might need
				if ($sortingIsSupported && array_key_exists($name,$sortableFields)) {
					$url->sort = (isset($_GET['sort'][$name]) && $_GET['sort'][$name]==1)
						? $url->sort = array($name=>-1)
						: $url->sort = array($name=>1);
					$class = $name==$currentSortField ? "class=\"$currentSortDirection\"" : '';
					echo "
					<th><a href=\"$url\" $class>$label</a></th>
					";
				}
				else {
					echo "<th>$label</th>";
				}
			}
		}
	}
	// Remember to leave an empty cell for any buttons we might want to add in the results
echo "
		<th></th>
	</tr>
</thead>
";