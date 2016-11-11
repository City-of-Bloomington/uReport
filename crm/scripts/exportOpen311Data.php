<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\Open311Client;

include realpath(__DIR__.'/../bootstrap.inc');

Open311Client::export_data(__DIR__.'/data.csv', 25);
