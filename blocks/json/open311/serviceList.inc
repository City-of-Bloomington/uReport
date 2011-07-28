<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param CategoryList $this->categoryList
 */
$services = array();
foreach ($this->categoryList as $category) {
	$services[] = array(
		'service_code'=>"{$category->getId()}",
		'service_name'=>$category->getName(),
		'type'=>'realtime',
		'description'=>$category->getDescription(),
		'metadata'=> count($category->getCustomFields()) ? true : false,
		'keywords'=>'',
		'group'=>''
	);
}
echo json_encode($services);