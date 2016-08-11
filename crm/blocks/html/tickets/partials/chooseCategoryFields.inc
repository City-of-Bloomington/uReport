<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param string $this->return_url
 */
use Application\Models\Category;
use Application\Models\CategoryTable;
use Blossom\Classes\Url;
?>
<div>
	<select id="category_id" name="category_id">
		<option value=""></option>
		<?php
			if (isset($_REQUEST['category_id'])) {
				try {
					$c = new Category($_REQUEST['category_id']);
					$name = self::escape($c->getName());
					echo "<option value=\"{$c->getId()}\" selected=\"selected\">$name</option>";
				}
				catch (Exception $e) {
					# Just ignore bad categories.  The user will just have to pick another
				}
			}

			$department = $_SESSION['USER']->getDepartment();
			if ($department) {
				foreach ($department->getCategories(['active'=>true]) as $c) {
					$name = self::escape($c->getName());
					echo "<option value=\"{$c->getId()}\">$name</option>";
				}
			}
			echo '<option value="">----</option>';

			$table = new CategoryTable();
			$list  = $table->find(['active'=>true], 'categories.name');
			$options = [];
			foreach ($list as $c) {
                $name = self::escape($c->getName());
                $d    = $c->getDepartment();
                $dept = self::escape($d->getName());
                $options[$dept][] = ['id'=>$c->getid(), 'name'=>self::escape($c->getName())];
			}
			ksort($options);
			foreach (array_keys($options) as $d) {
                echo "<optgroup label=\"$d\">";
                foreach ($options[$d] as $c) {
                    echo "<option value=\"$c[id]\">$c[name]</option>";
                }
                echo "</optgroup>";
			}
		?>
	</select>
</div>
