<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class CustomFieldJSON
{
	public function __construct() { }

	public function customFieldJSON($json)
	{
		$output = '';
		$j = json_decode($json);
		if ($j) {
			$output.= "<table>";
			foreach ($j as $data) {
				$name = View::escape($data->name);
				$label = $data->label ? View::escape($data->label) : '';
				$value = is_array($data->value) ? implode(', ',$data->value) : $data->value;
				$value = View::escape($value);
				$output.= "<tr><th>$name</th><td>$label</td><td>$value</td></tr>";
			}
			$output.= "</table>";
		}
		return $output;
	}
}