<?php
/**
 * @copyright 2023-2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);

use Application\Models\Person;
use Application\Models\PersonTable;

include '../bootstrap.php';
$table  = new PersonTable();
$person = new Person(XXXX);
print_r($person);
$people = $table->search(['firstname'=>'', 'lastname'=>'']);
foreach ($people['rows'] as $p) {
    if ($p->getId() != $person->getId()) {
        echo "Merge {$p->getId()} into {$person->getId()}\n";
        $person->mergeFrom($p);
    }
}
