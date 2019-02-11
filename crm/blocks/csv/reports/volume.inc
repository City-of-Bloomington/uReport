<?php
/**
 * @copyright 2019 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);

use Application\Models\DepartmentTable;
use Application\Models\Report;

$table = new DepartmentTable();
$list  = $table->find();

header("Content-Disposition: attachment; filename=\"volume.csv\"");
echo "Department,Category,Count\n";

$out = fopen('php://output', 'w');
foreach ($list as $d) {
    $data = Report::volumeByCategory($_GET, $d->getId());
    foreach ($data['result'] as $row) {
        fputcsv($out, [$d->getName(), $row['name'], $row['count']]);
    }
}
fclose($out);
