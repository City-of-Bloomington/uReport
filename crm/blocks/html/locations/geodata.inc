<?php
/**
 * @copyright 2016-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param string $this->location
 * @param Ticket $this->ticket   (optional)
 */
use Application\Models\AddressService;

$ticketFields  = '';
$addressFields = '';

if ($this->ticket) {
    $locationFields = ['city', 'state', 'zip', 'latitude', 'longitude'];
    foreach ($locationFields as $field) {
        $get = 'get'.ucfirst($field);
        $$field = self::escape($this->ticket->$get());
        if ($$field) {
            $ticketFields.= "<tr><th>{$this->_($field)}</th><td>{$$field}</td></tr>";
        }
    }

    if ($latitude && $longitude) {
        echo "
        <div id=\"location_map\" data-latitude=\"$latitude\" data-longitude=\"$longitude\"></div>
        <script type=\"text/javascript\">
            var DEFAULT_LATITUDE  = ".DEFAULT_LATITUDE.";
            var DEFAULT_LONGITUDE = ".DEFAULT_LONGITUDE.";
        </script>
        ";
        $this->template->addToAsset('scripts', GOOGLE_MAPS);
        $this->template->addToAsset('scripts', BASE_URI.'/js/tickets/map-'.VERSION.'.js');
    }
}
if (defined('ADDRESS_SERVICE')) {
    $customFields = call_user_func(ADDRESS_SERVICE.'::customFieldDefinitions');
    $data         = $this->ticket->getAdditionalFields();
    if ($data) {
        foreach ($customFields as $key=>$def) {
            if (!empty($data->$key)) {
                $value         = parent::escape($data->$key);
                $label         = $def['description'];
                $addressFields.= "<tr><th>$label</th><td>$value</td></tr>";
            }
        }
    }
}

echo "
<table class=\"geodata\">
    $ticketFields
    $addressFields
</table>
";
