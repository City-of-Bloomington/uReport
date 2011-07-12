<?php
/**
 * Displays a list of Tickets split up by category
 *
 * If categories are chosen in the search, we only display those categories.
 * If categories are not chosen, we will go through all the categories in the system
 *
 * This page only displays Ticket and Issue fields that are asked for
 * The fields desired need to be the indexes of an associative array
 * The values for each field just needs to be truthy
 *
 * $exampleFields = array('ticket-id'=>1,'ticket-status'=>1,'issue-issueType'=>1);
 *
 * For the full list of possible fields for use here,
 * See: TicketList::getDisplayableFields()
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->search The search to be passed to the TicketList
 * @param array $this->fields The ticket fields to display.
 * @param array $this->sort (optional) A mongo array that defines the sort
 */
if (isset($this->search['issues.category._id'])) {
	// Remove the whole set of categories.  Later, we'll replace each category
	// into the search and run the ticketList
	$categories = $this->search['issues.category._id'];
}
else {
	$categories = new CategoryList();
	$categories->find();
}
echo "
<div class=\"searchResults\">
	<h1>Category Report</h1>
";
	// Set up a table of contents
	echo '<ul class="tableOfContents">';

	// Buffer the results so we can display them after the tableOfContents
	$ticketData = '';

	foreach ($categories as $category) {
		// Create category objects if necessary
		if (!$category instanceof Category) {
			$category = new Category($category);
		}
		$id = preg_replace('/[^a-zA-Z]/','',$category->getName());
		$categoryName = View::escape($category->getName());

		$this->vars['search']['issues.category._id'] = array("{$category->getId()}");

		if ($this->sort) {
			$ticketList = new TicketList();
			$ticketList->find($this->search,$this->sort);
		}
		else {
			$ticketList = new TicketList($this->search);
		}
		$count = count($ticketList);
		if ($count) {
			// Render the tableOfContents link
			echo "<li><a href=\"$_SERVER[REQUEST_URI]#$id\">$categoryName ($count)</a></li>";

			// Buffer the results so we can display them after the tableOfContents
			$ticketData.= "
			<h2 id=\"$id\">$categoryName</h2>
			<table>
				<thead>
					<tr>
			";
					if (isset($_GET['sort'])) {
						$keys = array_keys($_GET['sort']);
						$currentSortField = $keys[0];
						$currentSortDirection = $_GET['sort'][$currentSortField]==1 ? 'ascending' : 'descending';
					}
					else {
						$currentSortField = 'enteredDate';
						$currentSortDirection = 'descending';
					}
					$fields = TicketList::getDisplayableFields();
					$url = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
					foreach ($this->fields as $name=>$value) {
						switch ($name) {
							case 'notes':
							case 'categories':
								$ticketData.= "<th>$fields[$name]</th>";
								break;

							default:
								$url->sort = (isset($_GET['sort'][$name]) && $_GET['sort'][$name]==1)
									? $url->sort = array($name=>-1)
									: $url->sort = array($name=>1);
								$class = $name==$currentSortField ? "class=\"$currentSortDirection\"" : '';
								$ticketData.= "<th><a href=\"$url\" $class>$fields[$name]</a></th>";
						}
					}
			$ticketData.= "
						<th></th>
					</tr>
				</thead>
				<tbody>
			";
				foreach ($ticketList as $ticket) {
					$ticketData.= '<tr>';
					foreach ($this->fields as $field=>$value) {
						$get = 'get'.ucfirst($field);
						$value = $ticket->$get();
						if (preg_match('/Person/',$field) || $field=='constituent') {
							if ($value) {
								$value = $value['fullname'];
							}
						}
						if (false !== strpos($field,'Date')) {
							if (isset($value->sec)) {
								$value = date(DATE_FORMAT,$value->sec);
							}
						}
						if ($field=='categories') {
							$c = array();
							foreach ($value as $category) {
								$c[] = $category['name'];
							}
							$value = implode(',',$c);
						}
						if ($field=='notes') {
							$value = isset($value[0]) ? $value[0] : '';
						}
						$value = View::escape($value);
						$ticketData.= "<td><a href=\"{$ticket->getURL()}\">$value</a></td>";
					}
					$ticketData.= '</tr>';
				}
			$ticketData.= "
				</tbody>
			</table>
			<a href=\"$_SERVER[REQUEST_URI]#banner\" class=\"top\">Back to Top</a>
			";
		}
	}
	echo "
	</ul>
	$ticketData
</div>
";
