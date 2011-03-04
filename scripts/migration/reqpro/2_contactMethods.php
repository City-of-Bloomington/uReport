<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include '../../../configuration.inc';
include './migrationConfig.inc';

$pdo = new PDO(MIGRATION_DSN,MIGRATION_USER,MIGRATION_PASS);

$query = $pdo->query('select distinct complaint_source from ce_eng_comp where complaint_source is not null');
foreach ($query->fetchAll(PDO::FETCH_COLUMN) as $method) {
	$contactMethod = new ContactMethod();
	$contactMethod->setName($method);
	$contactMethod->save();
	echo $contactMethod->getName()."\n";
}
