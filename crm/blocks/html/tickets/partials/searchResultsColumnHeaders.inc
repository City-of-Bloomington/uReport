<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->tickets
 */
echo "
<thead>
	<tr><td>Sort by:</td>
";
	if (count($this->tickets)) {
		$currentSort = Search::$defaultSort;
		if (isset($_GET['sort'])) {
			$keys = array_keys($_GET['sort']);
			$currentSort['field'] = $keys[0];
			$currentSort['order'] = ($_GET['sort'][$keys[0]] == SolrQuery::ORDER_ASC)
				? SolrQuery::ORDER_ASC
				: SolrQuery::ORDER_DESC;
		}

		$url = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
		$displayableFields = TicketList::getDisplayableFields();
		$sortableFields = Search::getSortableFields('ticket');

		// Loop through all the fields the user asked to see
		foreach ($_GET['fields'] as $name=>$value) {
			// Make sure they're allowed to see this field
			if (array_key_exists($name,$displayableFields)
				&& (false === strpos($name,'Person') || userIsAllowed('people'))) {

				$label = $displayableFields[$name]['displayName'];

				// If this field is being used for sorting,
				// draw a url that reverses the sort order
				if (in_array($name, $sortableFields)) {
					$url->sort = ($currentSort['field'] == $name
								  && $currentSort['order'] == SolrQuery::ORDER_ASC)
						? array($name=>SolrQuery::ORDER_DESC)
						: array($name=>SolrQuery::ORDER_ASC);
					$class = '';
					if ($name == $currentSort['field']) {
						$class = $currentSort['order'] == SolrQuery::ORDER_ASC
							? 'class="ascending"'
							: 'class="descending"';
					}
					echo "<th><a href=\"$url\" $class>$label</a></th>";
				}
				else {
					echo "<th>$label</th>";
				}
			}
		}
	}
	// Remember to leave an empty cell for any buttons we might want to add in the results
echo "
	</tr>
</thead>
";