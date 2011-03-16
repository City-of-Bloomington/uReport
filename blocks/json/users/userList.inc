<?php
/**
 * @copyright 2007-2010 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param UserList $this->userList
 */
$users = array();
foreach ($this->userList as $user) {
	$u = array(
		'id'=>$user->getId(),
		'name'=>$user->getPerson()->getFullname(),
		'username'=>$user->getUsername(),
		'person_id'=>$user->getPerson_id(),
		'authenticationMethod'=>$user->getAuthenticationMethod(),
		'roles'=>$user->getRoles()
	);
	if ($user->getDepartment()) {
		$u['department'] = array(
			'id'=>$user->getDepartment_id(),'name'=>$user->getDepartment()->getName()
		);
	}
	$users[] = $u;
}
echo json_encode($users);