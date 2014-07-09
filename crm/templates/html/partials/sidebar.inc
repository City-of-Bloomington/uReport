<?php
use Application\Models\Person;

include APPLICATION_HOME.'/templates/html/partials/panel-widgets/MyAccount.inc';
if (Person::isAllowed('users')) {
	include APPLICATION_HOME.'/templates/html/partials/panel-widgets/Admin.inc';
}
