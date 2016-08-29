<?php
/**
 * @copyright 2014-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\Report;
$this->_include('reports/parameters.inc');

$departmentData = Report::VolumeByDepartment($_GET);
?>
<section id="volumeReport" class="searchResults">
    <header>
        <h1><?= "{$this->_(['ticket', 'tickets', $departmentData['totalCount']])} ($departmentData[totalCount] total)"; ?></h1>
    </header>
    <?php
        foreach ($departmentData['result'] as $row) {
            $percentage = round($row['count'] / $departmentData['totalCount'] * 100, 2);
            echo '<table>';

            echo "
            <table>
            <thead>
                <tr class=\"department\">
                    <th>$row[name]</th>
                    <th>$percentage%</th>
                    <th>$row[count]</th>
                </tr>
            </thead>
            <tbody>
            ";

            $categoryData = Report::VolumeByCategory($_GET, $row['id']);
            foreach ($categoryData['result'] as $row) {
                $percentage = round($row['count'] / $departmentData['totalCount'] * 100, 2);
                echo "
                <tr><td>$row[name]</td>
                    <td>$percentage%</td>
                    <td>$row[count]</td>
                </tr>
                ";
            }
            echo "
            </tbody>
            </table>
            ";
        }
    ?>
</section>
