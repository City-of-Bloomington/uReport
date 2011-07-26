<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 * @param Issue $this->issue
 * @param int $this->index
 */
$category = $this->ticket->getCategory();
if ($category) {
	$customFields = $category->getCustomFields();
	if (count($customFields)) {
		echo "<table>";
		foreach ($customFields as $name=>$definition) {
			$type = isset($definition['type']) ? $definition['type'] : 'text';
			$label = isset($definition['label']) ? View::escape($definition['label']) : View::escape($definition['name']);
			$value = $this->issue->getData("customFields.$definition[name]");

			$field_name = "issue[customFields][$definition[name]]";
			$field_id = "issue-customFields-$definition[name]";
			switch ($type) {
				case 'radio':
					$value = View::escape($value);
					echo "
					<tr><td>$label</td>
						<td>
					";
						foreach ($definition['values'] as $v) {
							$v = View::escape($v);
							$checked = $v==$value ? 'checked="checked"' : '';
							echo "
							<label>
								<input type=\"radio\" name=\"$field_name\" value=\"$v\" $checked />
								$v
							</label>
							";
						}
					echo "
						</td>
					</tr>
					";
					break;
				case 'checkbox':
					if (!is_array($value)) {
						$value = array();
					}
					echo "
					<tr><td>$label</td>
						<td>
					";
						foreach ($definition['values'] as $v) {
							$checked = in_array($v,$value) ? 'checked="checked"' : '';
							$v = View::escape($v);
							echo "
							<label>
								<input type=\"checkbox\" name=\"{$field_name}[$v]\" value=\"$v\" $checked />
								$v
							</label>
							";
						}
					echo "
						</td>
					</tr>
					";
					break;
				case 'select':
					$value = View::escape($value);
					echo "
					<tr><td><label for=\"$field_id\">$label</td>
						<td><select name=\"$field_name\" id=\"$field_id\">
								<option></option>
					";
							foreach ($definition['values'] as $v) {
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
				case 'textarea':
					$value = View::escape($value);
					echo "
					<tr><td colspan=\"2\">
							<div><label for=\"$field_id\">$label</label></div>
							<textarea name=\"$field_name\" id=\"$field_id\" rows=\"3\" cols=\"50\">$value</textarea>
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
