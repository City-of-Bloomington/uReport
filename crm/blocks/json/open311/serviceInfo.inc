<?php
/**
 * @copyright 2011-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Category $this->category
 */
// Category customFields are Open311 attributes
// We adopted the Open311 standard for this feature
$attributes = [];
$customFields = $this->category->getCustomFields();
if ($customFields) {
    foreach ($customFields as $i=>$d) {
        $values = [];
        if (isset($d['values'])) {
            foreach ($d['values'] as $value) {
                $values[] = ['key'=>$value, 'name'=>$value];
            }
        }
        $order        = $i+1;
        $code         = $d['name'    ];
        $datatype     = $d['type'    ] ?? 'string';
        $description  = $d['label'   ] ?? $code;
        $required     = $d['required'] ?? false;
        $attributes[] = [
            'variable'    => true,
            'code'        => $code,
            'order'       => $order,
            'datatype'    => $datatype,
            'required'    => $required,
            'description' => $description,
            'values'      => $values,
            'datatype_description' => ''
        ];
    }
}
echo json_encode([
    'service_code' => (string)$this->category->getId(),
    'attributes'   => $attributes
]);
