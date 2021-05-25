<?php
/**
 * @copyright 2021 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param array $this->data  Raw database result
 */
declare (strict_types=1);
header("Content-Disposition: attachment; filename=\"data.csv\"");
$out = fopen('php://output', 'w');

foreach ($this->data as $row) {
    fputcsv($out, $row);
}
fclose($out);
