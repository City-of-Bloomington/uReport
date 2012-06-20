<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Category $this->category
 */
// Category customFields are Open311 attributes
// We adopted the Open311 standard for this feature
$attributes = array();
$customFields = $this->category->getCustomFields();
if ($customFields) {
	$attributes = $customFields;
}
echo json_encode(array(
	'service_code' => (string)$this->category->getId(),
	'attributes'   => $attributes
));
