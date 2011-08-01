<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Category $this->category
 */
$attributes = array();
$customFields = $this->category->getCustomFields();
if ($customFields) {
	foreach ($customFields as $i=>$definition) {
		$values = array();
		if (isset($definition['values'])) {
			foreach ($definition['values'] as $value) {
				$values[] = array('key'=>$value,'name'=>$value);
			}
		}
		$order = $i+1;
		$code  = View::escape($definition['name']);
		$datatype = isset($definition['type']) ? View::escape($definition['type']) : 'string';
		$description = isset($definition['label']) ? View::escape($definition['label']) : $code;
		$attributes[] = array(
			'variable'=>true,
			'code'=>$code,
			'order'=>$order,
			'datatype'=>$datatype,
			'datatype_description'=>'',
			'required'=>false,
			'description'=>$description,
			'values'=>$values
		);
	}
}
echo json_encode(array(
	'service_code'=>(string)$this->category->getId(),
	'attributes'=>$attributes
));
