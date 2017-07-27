#!/bin/bash
PHP=`which php`
CRM={{ ureport_install_path }}/crm

$PHP $CRM/scripts/ckan/updateCkanResources.php
