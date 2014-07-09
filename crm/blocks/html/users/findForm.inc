<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\DepartmentTable;
use Blossom\Classes\View;
?>
<div class="findForm">
	<form method="get" action="<?php echo $_SERVER['REDIRECT_URL']; ?>">
		<fieldset><legend><?php echo $this->translate('labels.find_user'); ?></legend>
			<table>
				<tr><td><label for="department_id">
							<?php echo $this->translate(array('labels.department', 'labels.departments', 1)); ?>
						</label>
					</td>
					<td><select name="department_id" id="department_id">
							<option></option>
							<?php
								$table = new DepartmentTable();
								$list = $table->find();
								foreach ($list as $department) {
									$name = View::escape($department->getName());
									$selected = (isset($_GET['department_id'])
												&& "{$department->getId()}"==$_GET['department_id'])
										? 'selected="selected"'
										: '';
									echo "<option value=\"{$department->getId()}\" $selected>$name</option>";
								}
							?>
						</select>
					</td>
				</tr>
			</table>
			<button type="submit"><span class="icon-search"></span> <?php echo $this->translate('submit'); ?></button>
		</fieldset>
	</form>
</div>
