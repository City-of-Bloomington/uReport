<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<div>
	<form method="get">
		<fieldset id="advanced-search">
			<div><h3>Category</h3>
				<ul>
				<?php
					$list = new CategoryList();
					$list->find();
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
			<div><h3>Department</h3>
				<ul>
				<?php
					$list = new DepartmentList();
					$list->find();
					foreach ($list as $d) {
						$checked = !empty($_GET['departments'][$d->getId()])
							? 'checked="checked"'
							: '';
						$name = View::escape($d->getName());
						echo "
						<li><label>
								<input name=\"departments[{$d->getId()}]\" type=\"checkbox\" $checked />
							$name
						</li>
						";
					}
				?>
				</ul>
			</div>
			<div><h3>Case Date</h3>
				<table>
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
				<?php
					$h = $this->template->getHelper('renderInputs');
					echo $h->renderInputs($_GET, null, array('enteredDate'));
				?>
				<button type="submit" class="submit">Submit</button>
			</div>
		</fieldset>
	</form>
</div>