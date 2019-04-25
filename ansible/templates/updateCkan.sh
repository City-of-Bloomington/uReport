#!/bin/bash
APPLICATION_HOME={{ ureport_install_path }}
SITE_HOME={{ ureport_site_home }}

SITE_HOME=$SITE_HOME php $APPLICATION_HOME/scripts/ckan/updateCkanResources.php
