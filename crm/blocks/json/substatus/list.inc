<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param SubstatusList $this->substatusList
 */
$output = array();
foreach ($this->substatusList as $s) {
	$output[] = array(
		'id'         => $s->getId(),
		'name'       => $s->getName(),
		'status'     => $s->getStatus(),
		'description'=> $s->getDescription()
	);
}
echo json_encode($output);