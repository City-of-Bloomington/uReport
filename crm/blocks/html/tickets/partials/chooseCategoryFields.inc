<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
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
<div><a href="<?php echo BASE_URL; ?>/categories/chooseCategory.php?return_url=<?php echo "{$this->return_url}"; ?>">
		Choose from all categories
	</a>
</div>
