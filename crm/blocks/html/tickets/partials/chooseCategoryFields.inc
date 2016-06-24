<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param string $this->return_url
 */
use Application\Models\Category;
use Blossom\Classes\View;
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

			if ($_SESSION['USER']->getDepartment_id()) {
				foreach ($_SESSION['USER']->getDepartment()->getCategories() as $c) {
					$name = self::escape($c->getName());
					echo "<option value=\"{$c->getId()}\">$name</option>";
				}
			}
		?>
	</select>
</div>
<div>
	<?php
		$url = new Url(BASE_URL.'/categories/choose');
		// Preserve any extra parameters passed in
		foreach ($_REQUEST as $key=>$value) {
			if ($key!='ticket_id' && $key!='return_url') {
				$url->$key = $value;
			}
		}
		$url->return_url = $this->return_url;
		echo "
		<a href=\"$url\" id=\"moreCategoriesLink\">
			{$this->_('more_categories')}
			<span class=\"fa fa-angle-double-right\"></span>
		</a>
		";
	?>
</div>
