<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param string $this->return_url
 */
$personQuery = isset($_GET['personQuery']) ? View::escape($_GET['personQuery']) : '';
if (!isset($_GET['setOfPeople'])) {
	$_GET['setOfPeople'] = 'all';
}
?>
<div class="findPeople yui3-skin-sam">
	<h1><?php echo $this->title ? View::escape($this->title) : 'Look up a Person'; ?></h1>
	<form method="get" action="<?php echo BASE_URL; ?>/people">
		<fieldset>
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
				$fields = array('firstname','lastname','email','organization');
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
			<?php
				echo $this->template->formButton(
					'Search','submit','search'
				);
				if (isset($this->return_url)) {
					$return_url = View::escape($this->return_url);
					echo "<input type=\"hidden\" name=\"return_url\" value=\"$return_url\" />";

					echo $this->template->formButton(
						'Cancel','button','cancel',null,"document.location.href='{$return_url}';"
					);
				}

			?>
		</fieldset>
	</form>
</div>
<?php
	$this->template->addToAsset('scripts',YUI.'/yui/yui-min.js');
	$this->template->addToAsset('scripts',BASE_URL.'/js/people/search.js');
?>