<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\Ckan;
use Application\Models\Open311Client;

include realpath(__DIR__.'/../../bootstrap.inc');
include __DIR__.'/Ckan.php';
$config = include realpath(__DIR__.'/site_config.inc');

$ckan = new Ckan($config);

foreach ($config['resource_categories'] as $resource_id=>$ureport) {
    $file = __DIR__."/$ureport[filename]";

    Open311Client::export_data($file, $ureport['category_id']);
    $ckan->upload_resource($resource_id, $file);
    # unlink($file);
}
