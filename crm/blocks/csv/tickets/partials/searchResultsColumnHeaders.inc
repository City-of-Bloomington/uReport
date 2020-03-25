<?php
/**
 * @copyright 2011-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Apache_Solr_Response $this->solrObject
 * @param array                $this->fields      Ticket fields the user asked to see
 */
use Application\Models\TicketTable;

$OUT               = fopen('php://output', 'w');
$labels            = [];
$displayableFields = TicketTable::getDisplayableFields();

// Loop through all the fields the user asked to see
// The key will be the fieldname and the value will be "On" or 1
// or whatever the browser gives to checkboxes when you post a form.
// We'll loop through all of the fieldnames that were checked
foreach ($this->fields as $name => $checked) {
    // Make sure they're allowed to see this field
    if (array_key_exists($name, $displayableFields)) {
        $labels[] = $displayableFields[$name];
    }
}

fputcsv($OUT, $labels);
fclose($OUT);
