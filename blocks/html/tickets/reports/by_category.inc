<?php
/**
 * Displays a list of Tickets split up by category
 *
 * If categories are chosen in the search, we only display those categories.
 * If categories are not chosen, we will go through all the categories in the system
 *
 * The fields desired need to be in the form of TicketList::getDisplayableFields()
 * Example:
 * $this->fieldsToDisplay = array (
 *		'enteredByPerson'=>array(
 *			'displayName'=>'Case Entered By',
 *			'searchOn'=>'enteredByPerson._id',
 *			'sortOn'=>'enteredByPerson.fullname'
 *		),
 *		'categories'=>array(
 *			'displayName'=>'Categories',
 *			'searchOn'=>'issues.category._id',
 *			'sortOn'=>'issues.category.name'
 *		)
 *	)
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

$returnFields = array();
foreach ($this->fieldsToDisplay as $field=>$definition) {
	$returnFields[] = $definition['sortOn'];
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

		$ticketList = new TicketList();
		$ticketList->findRawData($this->search,$this->sort,$returnFields);
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
				array('ticketList'=>$ticketList,'fieldsToDisplay'=>$this->fieldsToDisplay)
			);
			$ticketData.= $header->render('html',$this->template);

			$body = new Block(
				'tickets/partials/searchResultsRows.inc',
				array('ticketList'=>$ticketList,'fieldsToDisplay'=>$this->fieldsToDisplay)
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
