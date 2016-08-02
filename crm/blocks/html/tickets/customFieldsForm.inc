<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Category $this->category
 * @param Ticket   $this->ticket
 */
$category = $this->category;
if ($category) {
	$customFields = $category->getCustomFields();
	if (count($customFields)) {
		$ticketData = $this->ticket->getCustomFields();
		$helper = $this->template->getHelper('field');

		foreach ($customFields as $f) {
			$name  = $f->name;
			$type  = isset($f->type) ? $f->type : 'string';
			$label = isset($f->label) ? self::escape($f->label) : self::escape($name);
			$value = isset($ticketData->$name) ? $ticketData->$name : null;

			$field_name = "customFields[$name]";
			$field_id   = "customFields-$name";

			$inputParameters = [
                'name'  => $field_name,
                'id'    => $field_id,
                'label' => $label
			];
			switch ($type) {
				case 'singlevaluelist':
                    $options = [];
                    foreach ($f->values as $v) {
                        $v         = self::escape($v);
                        $options[] = ['value'=>$v, 'label'=>$v];
                    }
                    $inputParameters['value'  ] = self::escape($value);
                    $inputParameters['type'   ] = 'select';
                    $inputParameters['options'] = $options;
                    echo $helper->field($inputParameters);
					break;

				case 'multivaluelist':
                    $options = [];
                    foreach ($f->values as $v) {
                        $v = self::escape($v);
                        $options[] = ['value'=>$v, 'label'=>$v];
                    }
                    $inputParameters['value'  ] = is_array($value) ? $value : [];
                    $inputParameters['type'   ] = 'checkbox';
                    $inputParameters['options'] = $options;
                    echo $helper->field($inputParameters);
					break;

				case 'text':
					$inputParameters['value'] = self::escape($value);
					$inputParameters['type' ] = 'textarea';
					echo $helper->field($inputParameters);
					break;

				default:
                    $inputParameters['value'] = self::escape($value);
                    echo $helper->field($inputParameters);
			}
		}
	}
}
