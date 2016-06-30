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
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param CategoryList $this->categoryList
 * @param URL $this->return_url
 */
echo "
<section class=\"categoryChoices\">
    <header><h1>{$this->_('choose_category', 'messages')}</h1></header>
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
		$name        = self::escape($category->getName());
		$description = self::escape(trim($category->getDescription()));

		$dept = '';
		$department = $category->getDepartment();
		if ($department) {
            $dept = self::escape($department->getName());
		}

		if (isset($_REQUEST['callback'])) {
			$callback = preg_replace('/[^a-zA-Z_\.]/', '', $_REQUEST['callback']);
			$return_url = "javascript:self.opener.$callback({$category->getId()});";
		}
		else {
			$return_url->category_id = $category->getId();
		}
		echo "<li><a href=\"$return_url\" title=\"$description\">$name ($dept)</a></li>\n";
	}
echo "
	</ul>
</section>
";
