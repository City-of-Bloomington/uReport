#!/bin/bash
PHP=`which php`
CRM={{ ureport_install_path }}

$PHP $CRM/scripts/ckan/updateCkanResources.php
