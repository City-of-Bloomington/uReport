<?php
/**
 * Go through all ticket locations that have not been matched to the
 * ADDRESS_SERVICE and try to match them.  Update the address service data
 * for tickets that can be matched.
 */
use Application\Database;
use Application\Models\Ticket;

use Site\Classes\MasterAddress;

include '../bootstrap.inc';
$pdo     = Database::getConnection()->getDriver()->getConnection()->getResource();
$sql     = "select id, addressId, location, latitude, longitude
            from tickets
            where addressId is null
              and location  is not null
              and latitude  is not null
              and longitude is not null
            order by enteredDate desc";
$query   = $pdo->prepare($sql);
$query->execute();
$result  = $query->fetchAll(\PDO::FETCH_ASSOC);

$unknown = fopen('./unknown.txt', 'w');
$matches = fopen('./matches.txt', 'w');
foreach ($result as $row) {
    $data = MasterAddress::getLocationData($row['location']);
    if ($data) {
        $dist    = Ticket::distance((float)$data['latitude' ], (float)$data['longitude'],
                                    (float) $row['latitude' ], (float) $row['longitude']);
        $info    = "$row[id]: $row[location] => $data[location] $dist\n";
        echo $info;

        if ($dist < Ticket::CLOSE_ENOUGH) {
            $ticket = new Ticket($row['id']);
            $ticket->setAddressServiceData($data);
            $ticket->save();
            fwrite($matches, $info);
        }
        else {
            fwrite($unknown, $info);
        }
    }
}
