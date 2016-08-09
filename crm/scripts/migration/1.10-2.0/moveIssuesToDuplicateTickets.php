<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Blossom\Classes\Database;

include '../../../bootstrap.inc';

$zend_db = Database::getConnection();

$sql = "select id from actions where name='duplicate'";
$result = $zend_db->query($sql)->execute();
$row = $result->current();
$duplicate_action_id = (int)$row['id'];

$sql = "select id from people where username='inghamn'";
$result = $zend_db->query($sql)->execute();
$row = $result->current();
$inghamn = (int)$row['id'];

// Go through all the tickets that have more than one issue
$tickets = [];
$sql = "select ticket_id, count(*) as c from issues group by ticket_id having c>1";
$result = $zend_db->query($sql)->execute();
foreach ($result as $row) { $tickets[] = $row; }

$addNewTicketSql = "insert tickets
                    (parent_id, category_id, enteredByPerson_id, assignedPerson_id, enteredDate, addressId, latitude, longitude, location, city, state, zip, status, substatus_id, additionalFields, closedDate)
                    values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
$moveIssueSql = "update issues set ticket_id=? where id=?";
$createHistorySql = "insert ticketHistory
                    (ticket_id, enteredByPerson_id, action_id, enteredDate, actionDate, data)
                    values (?, ?, ?, ?, ?, ?)";
foreach ($tickets as $t) {
    $sql = "select * from tickets where id=?";
    $result = $zend_db->query($sql)->execute([$t['ticket_id']]);
    $ticket = $result->current();
    echo "\n\n\nTIKCET $ticket[id]\n";

    $issues = [];
    $sql = "select * from issues where ticket_id=?";
    $result = $zend_db->query($sql)->execute([$ticket['id']]);
    foreach ($result as $row) { $issues[] = $row; }

    foreach ($issues as $i=>$issue) {
        // Keep the first issue on the original ticket
        if ($i === 0) { continue; }

        echo "ISSUE $issue[id]\n";

        $zend_db->query($addNewTicketSql)->execute([
            $ticket['id'],
            $ticket['category_id'],
             $issue['enteredByPerson_id'],
            $ticket['assignedPerson_id'],
             $issue['date'],
            $ticket['addressId'],
            $ticket['latitude'],
            $ticket['longitude'],
            $ticket['location'],
            $ticket['city'],
            $ticket['state'],
            $ticket['zip'],
            $ticket['status'],
            $ticket['substatus_id'],
            $ticket['additionalFields'],
            $ticket['closedDate']
        ]);
        $newTicketId = $zend_db->getDriver()->getLastGeneratedValue();

        $zend_db->query($moveIssueSql)->execute([$newTicketId, $issue['id']]);

        $zend_db->query($createHistorySql)->execute([
            $ticket['id'],
            $inghamn,
            $duplicate_action_id,
            $issue['date'],
            $issue['date'],
            "{\"duplicate\":{\"ticket_id\":$newTicketId}}"
        ]);
    }
}
