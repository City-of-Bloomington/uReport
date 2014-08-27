<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\Report;
include APPLICATION_HOME.'/blocks/html/reports/parameters.inc';

$departmentData = Report::VolumeByDepartment($_GET);
?>
<style>
    tr.department { background-color:#ccc; color:#000; font-weight:bold; border-top:2px solid #000; }
</style>
<div id="volumeReport">
    <h2>Incoming Reports
        <?php
            echo "($departmentData[totalCount] total)";
        ?>
    </h2>
    <table>
        <thead>
            <tr><th></th>
                <th>Percentage</th>
                <th>Raw Count</th>
            </tr>
        </thead>
        <tbody>
        <?php
            foreach ($departmentData['result'] as $row) {
                $percentage = round($row['count'] / $departmentData['totalCount'] * 100, 2);
                echo "
                <tr class=\"department\">
                    <td>$row[name]</td>
                    <td>$percentage%</td>
                    <td>$row[count]</td>
                </tr>
                ";

                $categoryData = Report::VolumeByCategory($_GET, $row['id']);
                foreach ($categoryData['result'] as $row) {
                    $percentage = round($row['count'] / $departmentData['totalCount'] * 100, 2);
                    echo "
                    <tr>
                        <td>$row[name]</td>
                        <td>$percentage%</td>
                        <td>$row[count]</td>
                    </tr>
                    ";
                }
            }
        ?>
        </tbody>
    </table>
</div>
