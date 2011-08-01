<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TicketList $this->ticketList
 * @param array $this->fieldsToDisplay
 */
echo "
<thead>
	<tr>
";
	if (count($this->ticketList) < TicketList::$MAX_SORTABLE_ROWS) {
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
		foreach ($this->fieldsToDisplay as $name=>$definition) {
			switch ($name) {
				case 'notes':
					echo "<th>$definition[displayName]</th>";
					break;

				default:
					$url->sort = (isset($_GET['sort'][$name]) && $_GET['sort'][$name]==1)
						? $url->sort = array($name=>-1)
						: $url->sort = array($name=>1);
					$class = $name==$currentSortField ? "class=\"$currentSortDirection\"" : '';
					echo "
					<th><a href=\"$url\" $class>$definition[displayName]</a></th>
					";
			}
		}
	}
	else {
		foreach ($this->fieldsToDisplay as $name=>$definition) {
			echo "<th>$definition[displayName]</th>";
		}
	}

	// Remember to leave an empty cell for any buttons we might want to add in the results
echo "
		<th></th>
	</tr>
</thead>
";