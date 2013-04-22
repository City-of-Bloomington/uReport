<?php
/**
 * Displays the full list of all the categories in the system
 *
 * This allows a user to choose a category from the full list.
 * To use this block, set $return_url to where you want the user
 * to go after choosing.
 * Each category will be a link to $return_url containg
 * the category_id of the chosen category
 *
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param CategoryList $this->categoryList
 * @param URL $this->return_url
 */
echo "
<div class=\"categoryChoices\">
	<h2>Choose a category</h2>
	<ul>
";
	$return_url = $this->return_url;
	// Preserve any extra parameters passed in
	foreach ($_REQUEST as $key=>$value) {
		if ($key!='return_url') {
			$return_url->$key = $value;
		}
	}
	foreach ($this->categoryList as $category) {
		$name = View::escape($category->getName());
		$description = View::escape(trim($category->getDescription()));

		if (isset($_REQUEST['callback'])) {
			$callback = preg_replace('/[^a-zA-Z_\.]/', '', $_REQUEST['callback']);
			$return_url = "javascript:self.opener.$callback({$category->getId()});";
		}
		else {
			$return_url->category_id = $category->getId();
		}
		echo "<li><a href=\"$return_url\" title=\"$description\">$name</a></li>\n";
	}
echo "
	</ul>
</div>
";
