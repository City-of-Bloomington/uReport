<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param string $this->return_url
 */
?>
<label id="category">
	Choose a category
</label>
<div>
<select id="category" name="category_id">
	<?php
		if ($_SESSION['USER']->getDepartment_id()) {
			if (isset($_REQUEST['category_id'])) {
				try {
					$category = new Category($_REQUEST['category_id']);
					$name = View::escape($category->getName());
					echo "
					<option value=\"{$category->getId()}\" selected=\"selected\">$name</option>
					";
				}
				catch (Exception $e) {
					# Just ignore bad categories.  The user will just have to pick another
				}
			}

			foreach ($_SESSION['USER']->getDepartment()->getCategories() as $c) {
				$name = View::escape($c->getName());
				echo "<option value=\"{$c->getId()}\">$name</option>";
			}
		}
	?>
</select>
</div>

<div>
	<?php
		$url = new URL(BASE_URL.'/categories/choose');
		// Preserve any extra parameters passed in
		foreach ($_REQUEST as $key=>$value) {
			if ($key!='ticket_id' && $key!='return_url') {
				$url->$key = $value;
			}
		}
		$url->return_url = $this->return_url;
		echo "<a href=\"$url\">More categories</a>";
	?>
</div>
