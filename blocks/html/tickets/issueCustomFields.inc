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
			$type = isset($definition['type']) ? $definition['type'] : 'string';
			$label = isset($definition['label']) ? View::escape($definition['label']) : View::escape($definition['name']);
			$value = $this->issue->getData("customFields.$definition[name]");

			$field_name = "issue[customFields][$definition[name]]";
			$field_id = "issue-customFields-$definition[name]";
			switch ($type) {
				case 'singlevaluelist':
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
				case 'multivaluelist':
					if (!is_array($value)) {
						$value = array();
					}
					echo "
					<tr><td><label for=\"$field_id\">$label</label></td>
						<td><select name=\"$field_name\" id=\"$field_id\" multiple=\"true\" size=\"5\">
					";
							foreach ($definition['values'] as $v) {
								$selected = in_array($v,$value) ? 'selected="selected"' : '';
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
