<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Category $this->category
 */
$c = array(
	'id'                    => $this->category->getId(),
	'name'                  => $this->category->getName(),
	'description'           => $this->category->getDescription(),
	'categoryGroup_id'      => $this->category->getCategoryGroup_id(),
	'department_id'         => $this->category->getDepartment_id(),
	'displayPermissionLevel'=> $this->category->getDisplayPermissionLevel(),
	'postingPermissionLevel'=> $this->category->getPostingPermissionLevel(),
	'customFields'          => $this->category->getCustomFields()
);
echo json_encode($c);
