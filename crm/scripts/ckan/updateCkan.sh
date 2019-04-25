#!/bin/bash
APPLICATION_HOME=/srv/sites/ureport
SITE_HOME=$APPLICATION_HOME/data

SITE_HOME=$SITE_HOME php $APPLICATION_HOME/scripts/ckan/updateCkanResources.php
