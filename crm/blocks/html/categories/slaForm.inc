<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param CategoryList $this->categoryList
 */
?>
<div class="slaForm">
	<h2>Service Level Agreement</h2>
	<p>These departments agree to handle new tickets within the time specificed.</p>

	<form method="post" action="<?php echo BASE_URL; ?>/categories/sla">
		<fieldset><legend>Categories</legend>
			<table>
				<thead>
					<tr><th>Category</th><th>Department</th><th>SLA Time</th></tr>
				</thead>
				<tbody>
				<?php
					foreach ($this->categoryList as $category) {
						$name = View::escape($category->getName());
						$d = $category->getDepartment();
						$department = $d ? View::escape($d->getName()) : '';

						$field = "categories[{$category->getId()}]";
						echo "
						<tr><td>$name</td>
							<td>$department</td>
							<td><input type=\"hidden\"
									name=\"{$field}[id]\"
									value=\"{$category->getId()}\" />
								<input name=\"{$field}[slaExpression]\"
									size=\"2\" maxlength=\"2\"
									value=\"{$category->getSlaExpression()}\" />
								<select name=\"{$field}[slaUnits]\">
									<option></option>
						";
									foreach (Category::$SLA_UNITS as $unit) {
										$selected = $category->getSlaUnits()==$unit
											? 'selected="selected"'
											: '';
										echo "<option $selected>$unit</option>";
									}
						echo "
								</select>
							</td>
						</tr>
						";
					}
				?>
				</tbody>
			</table>

			<button type="submit" class="submit">Submit</button>
			<a class="cancel" href="<?php echo BASE_URI; ?>/categories">Cancel</cancel>
		</fieldset>
	</form>
</div>