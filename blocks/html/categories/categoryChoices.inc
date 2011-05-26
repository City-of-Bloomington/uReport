<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param CategoryList $this->categoryList
 * @param URL $this->return_url
 */
echo "
<div class=\"categoryChoices\">
	<ul>
";
	$return_url = $this->return_url;
	foreach ($this->categoryList as $category) {
		$name = View::escape($category->getName());
		$description = View::escape(trim($category->getDescription()));
		$return_url->category_id = "{$category->getId()}";
		echo "\t\t<li><a href=\"$return_url\" title=\"$description\">$name</a></li>\n";
	}
echo "
	</ul>
</div>
";
