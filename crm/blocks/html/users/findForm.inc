<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<div class="findForm">
	<form method="get" action="<?php echo $_SERVER['REDIRECT_URL']; ?>">
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
			<button type="submit" class="submit">Submit</button>
		</fieldset>
	</form>
</div>