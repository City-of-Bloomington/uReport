<?php
/**
 * @copyright 2011-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param CategoryList $this->categoryList
 */
$services = [];
foreach ($this->categoryList as $category) {
	$s = [
		'service_code' => "{$category->getId()}",
		'service_name' => $category->getName(),
		'type'         => 'realtime',
		'description'  => $category->getDescription(),
		'metadata'     => $category->getCustomFields() ? true : false,
		'keywords'     => "",
		'group'        => "{$category->getCategoryGroup()}"
	];
	if ($category->isFeatured()) { $s['featured'] = 1; }
	$services[] = $s;
}
echo json_encode($services);
