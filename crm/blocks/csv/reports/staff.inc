<?php
/**
 * @copyright 2019 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param array $this->data  Raw database result
 */
declare (strict_types=1);

header("Content-Disposition: attachment; filename=\"staff.csv\"");
echo "Firstname,Lastname,Open,Closed,Average Days Responsible\n";

$out = fopen('php://output', 'w');

foreach ($this->data as $row) {
    fputcsv($out, [
        $row['firstname'],
        $row['lastname' ],
        $row['open'     ],
        $row['closed'   ],
        $row['average'  ]
    ]);
}
fclose($out);
