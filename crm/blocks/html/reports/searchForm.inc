<?php
/**
 * @copyright 2012-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\CategoryTable;
use Application\Models\Department;
use Blossom\Classes\View;

$this->template->addToAsset('scripts',YUI.'/yui/yui-min.js');
$this->template->addToAsset('scripts',BASE_URL.'/js/collapsible.js');
?>
<div>
	<form method="get">
		<fieldset>
			<div class="collapsible">
				<h3 class="hd">Category</h3>
				<ul class="bd">
				<?php
					$t = new CategoryTable();
					$list = $t->find();
					foreach ($list as $c) {
						$checked = !empty($_GET['categories'][$c->getId()])
							? 'checked="checked"'
							: '';
						$name = View::escape($c->getName());
						echo "
						<li><label>
								<input name=\"categories[{$c->getId()}]\" type=\"checkbox\" $checked />
								$name
							</label>
						</li>
						";
					}
				?>
				</ul>
			</div>
			<div class="collapsible">
				<h3 class="hd">Department</h3>
				<ul class="bd">
				<?php
					$t = new DepartmentTable();
					$list = $t->find();
					foreach ($list as $d) {
						$checked = !empty($_GET['departments'][$d->getId()])
							? 'checked="checked"'
							: '';
						$name = View::escape($d->getName());
						echo "
						<li><label>
								<input name=\"departments[{$d->getId()}]\" type=\"checkbox\" $checked />
								$name
							</label>
						</li>
						";
					}
				?>
				</ul>
			</div>
			<div class="collapsible">
				<h3 class="hd">Case Date</h3>
				<table class="bd">
					<tr><td><label for="enteredDate-start">Start:</label></td>
						<td><input name="enteredDate[start]" id="enteredDate-start" size="10" maxlength="10"
								value="<?php echo !empty($_GET['enteredDate']['start']) ? View::escape($_GET['enteredDate']['start']) : ''; ?>" />
						</td>
					</tr>
					<tr><td><label for="enteredDate-end">End:</label></td>
						<td><input name="enteredDate[end]" id="enteredDate-end" size="10" maxlength="10"
								value="<?php echo !empty($_GET['enteredDate']['end']) ? View::escape($_GET['enteredDate']['end']) : ''; ?>" />
						</td>
					</tr>
				</table>
			</div>
			<?php
				$h = $this->template->getHelper('renderInputs');
				echo $h->renderInputs($_GET, null, array('enteredDate'));
			?>
			<button type="submit"><span class="icon-ok"></span> Submit</button>
		</fieldset>
	</form>
</div>
