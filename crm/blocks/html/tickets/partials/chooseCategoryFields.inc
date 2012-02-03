<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param string $this->return_url
 */
?>
<label>
	Category
	<select name="category_id">
	<?php
		if ($_SESSION['USER']->getDepartment()) {
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

			$dept = $_SESSION['USER']->getDepartment();
			$department = new Department($dept['_id']);

			foreach ($department->getCategories() as $departmentCategory) {
				$name = View::escape($departmentCategory['name']);
				echo "<option value=\"$departmentCategory[_id]\">$name</option>";
			}
		}
	?>
	</select>
</label>
<div><a href="<?php echo BASE_URL; ?>/categories/choose?return_url=<?php echo "{$this->return_url}"; ?>">
		Choose from all categories
	</a>
</div>
