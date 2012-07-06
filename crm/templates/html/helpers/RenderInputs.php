<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class RenderInputs
{
	public function __construct() { }

	/**
	 * Converts an array into hidden inputs for a form
	 *
	 * Used for preserving all $_REQUEST information in subsequent form posts
	 *
	 * @param array  $array      Usually the $_REQUEST array
	 * @param string $base       A key used for naming inputs as an array
	 * @param array  $filterKeys Keys in $array to be ignored
	 */
	public function renderInputs($array, $base=null, $filterKeys=null)
	{
		$html = '';
		foreach ($array as $k=>$v) {
			if (!$filterKeys || !in_array($k, $filterKeys)) {
				$k = View::escape($k);
				$name = $base ? "{$base}[{$k}]" : $k;
				if (!is_array($v)) {
					$v = View::escape($v);
					$html.= "<input name=\"$name\" value=\"$v\" type=\"hidden\" />";
				}
				else {
					$this->renderInputs($v, $k, $filterKeys);
				}
			}
		}
		return $html;
	}
}