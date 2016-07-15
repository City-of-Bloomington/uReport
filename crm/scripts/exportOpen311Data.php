<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Blossom\Classes\Database;

include '../bootstrap.inc';

$fields = [
    'service_request_id',
    'requested_datetime',
    'updated_datetime',
    'closed_date',
    'status',
    'source',
    'service_name',
    'service_subtype',
    'description',
    'agency_responsible',
    'address',
    'lat',
    'long'
];

$FILE = fopen('./data.csv', 'w');
fputcsv($FILE, $fields);

$zend_db = Database::getConnection();
$sql = "select  t.id            as ticket_id,
                t.enteredDate,
                t.lastModified,
                t.closedDate,
                t.status,
                m.name          as contactMethod,
                c.name          as category,
                i.description,
                d.name          as department,
                t.location, t.city, t.state, t.zip,
                t.latitude,
                t.longitude
        from issues           i
        join tickets          t on i.ticket_id=t.id
        left join categories  c on t.category_id=c.id
        left join people      p on t.assignedPerson_id=p.id
        left join departments d on p.department_id=d.id
        left join contactMethods m on i.contactMethod_id=m.id
        where c.displayPermissionLevel = 'anonymous'";
$result = $zend_db->query($sql)->execute();
foreach ($result as $row) {
    $enteredDate  = new \DateTime($row['enteredDate']);
    $lastModified = new \DateTime($row['lastModified']);
    $closedDate   = new \DateTime($row['closedDate']);

    $location = '';
    if (!empty($row['location'])) {
        $l = [$row['location']];
        if (!empty($row['city' ])) { $l[] = $row['city' ]; }
        if (!empty($row['state'])) { $l[] = $row['state']; }
        if (!empty($row['zip'  ])) { $l[] = $row['zip'  ]; }

        $location = implode(', ', $l);
    }

    $data = [
        'service_request_id' => $row['ticket_id'],
        'requested_datetime' => $enteredDate ->format('c'),
        'updated_datetime'   => $lastModified->format('c'),
        'closed_date'        => $closedDate  ->format('c'),
        'status'             => $row['status'],
        'source'             => $row['contactMethod'],
        'service_name'       => $row['category'],
        'service_subtype'    => '',
        'description'        => $row['description'],
        'agency_responsible' => $row['department'],
        'address'            => $location,
        'lat'                => $row['latitude'],
        'long'               => $row['longitude']
    ];
    fputcsv($FILE, $data);
}
fclose($FILE);
