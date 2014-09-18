<?php
/**
 * @copyright 2011-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Blossom\Classes\View;

$fields = array('firstname','lastname','email','organization');
if (!isset($_GET['setOfPeople'])) {
	$_GET['setOfPeople'] = 'all';
}
?>
<div class="findPeople">
	<h2><?php echo $this->title ? View::escape($this->title) : $this->translate('labels.find_person'); ?></h2>
	<form method="get" action="<?php echo BASE_URL; ?>/people">
		<fieldset>
			<?php
				// Preserve any extra parameters passed in
				foreach ($_REQUEST as $key=>$value) {
					if ($key!='setOfPeople' && !in_array($key,$fields)) {
						$key   = View::escape($key);
						$value = View::escape($value);
						echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />\n";
					}
				}
			?>
			<div><label>
					<input type="radio" name="setOfPeople" value="all"
						<?php if ($_GET['setOfPeople']=='all') echo 'checked="checked"'; ?> />
					All
				</label>
			</div>
			<div><label>
					<input type="radio" name="setOfPeople" value="staff"
						<?php if ($_GET['setOfPeople']=='staff') echo 'checked="checked"'; ?> />
					Staff Only
				</label>
			</div>
			<div><label>
					<input type="radio" name="setOfPeople" value="public"
						<?php if ($_GET['setOfPeople']=='public') echo 'checked="checked"'; ?> />
					Public Only
				</label>
			</div>
		</fieldset>
		<fieldset>
			<table>
			<?php
				foreach ($fields as $field) {
					$label = ucfirst($field);
					$value = isset($_GET[$field]) ? View::escape($_GET[$field]) : '';
					echo "
					<tr><td><label for=\"$field\">$label</label></td>
						<td><input name=\"$field\" id=\"$field\" value=\"$value\" /></td>
					</tr>
					";
				}
			?>
			</table>
			<button type="submit"><span class="fa fa-search"></span> <?php echo $this->translate('labels.search'); ?></button>
			<?php
				if (isset($_REQUEST['callback'])) {
					echo "
					<button type=\"button\" onclick=\"self.close();\">
						<span class=\"fa fa-times\"></span>
						{$this->translate('labels.cancel')}
					</button>
					";
				}
			?>
		</fieldset>
	</form>
</div>
<?php
	$this->template->addToAsset('scripts', JQUERY.'/jquery.min.js');
	$this->template->addToAsset('scripts', JQUERY_UI.'/jquery-ui.min.js');
	$this->template->addToAsset('scripts', BASE_URI.'/js/people/search.js');
?>
