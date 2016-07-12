<?php
/**
 * @copyright 2012-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\CategoryTable;
use Application\Models\Department;
use Application\Models\DepartmentTable;
use Application\Models\ClientTable;

use Blossom\Classes\View;

$this->template->addToAsset('scripts', JQUERY.'/jquery.min.js');
$this->template->addToAsset('scripts', BASE_URI.'/js/collapsible.js');
?>
<div>
    <h2>Filters</h2>
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
						$name = self::escape($c->getName());
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
						$name = self::escape($d->getName());
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
								value="<?= !empty($_GET['enteredDate']['start']) ? self::escape($_GET['enteredDate']['start']) : ''; ?>" />
						</td>
					</tr>
					<tr><td><label for="enteredDate-end">End:</label></td>
						<td><input name="enteredDate[end]" id="enteredDate-end" size="10" maxlength="10"
								value="<?= !empty($_GET['enteredDate']['end']) ? self::escape($_GET['enteredDate']['end']) : ''; ?>" />
						</td>
					</tr>
				</table>
			</div>
			<div class="collapsible">
                <h3 class="hd">Reportable By:</h3>
                <div class="bd">
                    <?php
                        foreach (['anonymous', 'staff'] as $v) {
                            $checked = (!empty($_GET['postingPermissionLevel']) && $_GET['postingPermissionLevel']==$v)
                                ? 'checked="checked"'
                                : '';
                            echo "
                            <div><label>$v
                                    <input name=\"postingPermissionLevel\" type=\"radio\" value=\"$v\" $checked/>
                                </label>
                            </div>
                            ";
                        }
                    ?>
                </div>
			</div>
            <div class="collapsible">
                <h3 class="hd"><?= $this->_(['client', 'clients', 2]); ?></h3>
                <div class="bd">
                <?php
                    $table = new ClientTable();
                    $list = $table->find();
                    foreach ($list as $c) {
                        $checked = !empty($_GET['clients'][$c->getId()])
                            ? 'checked="checked"'
                            : '';
                        $name = self::escape($c->getName());
                        echo "
                        <div><label>
                                <input name=\"clients[{$c->getId()}]\" type=\"checkbox\" $checked />
                                $name
                            </label>
                        </div>
                        ";
                    }
                ?>
                </div>
            </div>
			<?php
				$h = $this->template->getHelper('renderInputs');
				echo $h->renderInputs($_GET, null, array('enteredDate'));
			?>
			<button type="submit" class="submit"><?= $this->template->_('submit'); ?></button>
		</fieldset>
	</form>
</div>
