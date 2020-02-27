<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Category $this->category
 */
// Category customFields are Open311 attributes
// We adopted the Open311 standard for this feature
$attributes = array();
$customFields = $this->category->getCustomFields();
if ($customFields) {
	foreach ($customFields as $i=>$d) {
		$values = array();
		if (isset($d->values)) {
			foreach ($d->values as $value) {
				$values[] = array('key'=>$value,'name'=>$value);
			}
		}
		$order = $i+1;
		$code  = $d->name;
		$datatype    = isset($d->type)     ? $d->type     : 'string';
		$description = isset($d->label)    ? $d->label    : $code;
		$required    = isset($d->required) ? $d->required : false;
		$attributes[] = array(
			'variable'=>true,
			'code'=>$code,
			'order'=>$order,
			'datatype'=>$datatype,
			'datatype_description'=>'',
			'required'=>$required,
			'description'=>$description,
			'values'=>$values
		);
	}
}
echo json_encode(array(
	'service_code' => (string)$this->category->getId(),
	'attributes'   => $attributes
));
