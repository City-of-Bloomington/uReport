<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Category $this->category
 * @param Issue $this->issue
 */
use Blossom\Classes\View;

echo "<div id=\"customFields\">";
$category = $this->category;
if ($category) {
	$customFields = $category->getCustomFields();
	if (count($customFields)) {
		echo "<table>";
		foreach ($customFields as $f) {
			$name = $f->name;
			$type = isset($f->type) ? $f->type : 'string';
			$label = isset($f->label) ? View::escape($f->label) : View::escape($name);

			$issueData = $this->issue->getCustomFields();
			$value = isset($issueData->$name) ? $issueData->$name : null;

			$field_name = "customFields[$name]";
			$field_id   = "customFields-$name";
			switch ($type) {
				case 'singlevaluelist':
					$value = View::escape($value);
					echo "
					<tr><td><label for=\"$field_id\">$label</td>
						<td><select name=\"$field_name\" id=\"$field_id\">
								<option></option>
					";
							foreach ($f->values as $v) {
								$v = View::escape($v);
								$selected = $v==$value ? 'selected="selected"' : '';
								echo "<option value=\"$v\" $selected>$v</option>";
							}
					echo "
							</select>
						</td>
					</tr>
					";
					break;
				case 'multivaluelist':
					if (!is_array($value)) {
						$value = array();
					}
					echo "
					<tr><td><label for=\"$field_id\">$label</label></td>
						<td><select name=\"{$field_name}[]\" id=\"$field_id\" multiple=\"true\" size=\"5\">
					";
							foreach ($f->values as $v) {
								$selected = in_array($v, $value) ? 'selected="selected"' : '';
								$v = View::escape($v);
								echo "<option value=\"$v\" $selected>$v</option>";
							}
					echo "
							</select>
						</td>
					</tr>
					";
					break;
				case 'text':
					$value = View::escape($value);
					echo "
					<tr><td colspan=\"2\">
							<div><label for=\"$field_id\">$label</label></div>
							<textarea name=\"$field_name\" id=\"$field_id\">$value</textarea>
						</td>
					</tr>
					";
					break;
				default:
					$value = View::escape($value);
					echo "
					<tr><td><label for=\"$field_id\">$label</label></td>
						<td><input name=\"$field_name\" id=\"$field_id\" value=\"$value\" /></td>
					</tr>
					";
			}
		}
		echo "</table>";
	}
}
echo "</div>";
