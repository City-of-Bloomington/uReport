<?php
/**
 * Displays a list of Tickets split up by category
 *
 * If categories are chosen in the search, we only display those categories.
 * If categories are not chosen, we will go through all the categories in the system
 *
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
* @param array $this->sort (optional) A mongo array that defines the sort
 */
if (isset($_GET['category'])) {
	// Remove all the requested categories from the search
	// Later, we'll do a search using each one, one at a time
	$categories = $_GET['category'];
	unset($_GET['category']);
}
else {
	$categories = new CategoryList();
	$categories->find();
}

// Make sure Ticket Number is always displayed
$_GET['fields']['number'] = 1;


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

		$_GET['category'] = "{$category->getId()}";

		$ticketList = new TicketList();
		$ticketList->findRawData($_GET);
		$count = count($ticketList);

		if ($count) {
			// Render the tableOfContents link
			echo "<li><a href=\"$_SERVER[REQUEST_URI]#$id\">$categoryName ($count)</a></li>";

			// Buffer the results so we can display them after the tableOfContents
			$ticketData.= "
			<h2 id=\"$id\">$categoryName</h2>
			<table>
			";
			$header = new Block(
				'tickets/partials/searchResultsColumnHeaders.inc',
				array('ticketList'=>$ticketList)
			);
			$ticketData.= $header->render('html',$this->template);

			$body = new Block(
				'tickets/partials/searchResultsRows.inc',
				array('ticketList'=>$ticketList)
			);
			$ticketData.= $body->render('html',$this->template);

			$ticketData.= "
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
