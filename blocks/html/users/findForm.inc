<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<div class="findForm">
	<form method="get" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
		<fieldset><legend>Filter Users</legend>
			<table>
				<tr><td><label for="department_id">Department</label></td>
					<td><select name="department_id" id="department_id">
							<option></option>
							<?php
								$list = new DepartmentList();
								$list->find();
								foreach ($list as $department) {
									$name = View::escape($department->getName());
									$selected = (isset($_GET['department_id'])
												&& "{$department->getId()}"=="{$_GET['department_id']}")
										? 'selected="selected"'
										: '';
									echo "<option value=\"{$department->getId()}\">$name</option>";
								}
							?>
						</select>
					</td>
				</tr>
			</table>
			<?php
				echo $this->template->formButton('Submit','submit','submit');
			?>
		</fieldset>
	</form>
</div>