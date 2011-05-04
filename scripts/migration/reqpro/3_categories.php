<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include '../../../configuration.inc';
include './migrationConfig.inc';
$pdo = new PDO(MIGRATION_DSN,MIGRATION_USER,MIGRATION_PASS);

$sql = "select comp_desc from c_types where c_type1!=0";
$result = $pdo->query($sql);
foreach ($result->fetchAll(PDO::FETCH_COLUMN) as $name) {
	$category = new Category();
	$category->setName($name);

	if (preg_match('/NOTICE/',$name)) {
		list($type,$notice) = explode(' ',trim($name));
		$type = $type=='RECYCLING' ? 'RECYCLE' : $type;

		$query = $pdo->prepare('select notice from sanitation_notices where type=?');
		$query->execute(array($type));
		foreach ($query->fetchAll(PDO::FETCH_COLUMN) as $notice) {
			$category->updateProblems($notice);
		}
	}
	$category->save();
	echo $category->getName()."\n";
}

