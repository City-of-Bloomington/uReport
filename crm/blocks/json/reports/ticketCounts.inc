<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Laminas\Db\Result $this->data
 */
$o = [];
foreach ($this->data as $row) {
    $o[] = $row;
}
echo json_encode($o, JSON_PRETTY_PRINT);
