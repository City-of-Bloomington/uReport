<?php
use Application\Models\Person;
?>
<div id="menuBar">
	<ul><?php
			$li = '<li><a href="%s">%s</a></li>';
			echo sprintf($li, BASE_URI.'/tickets'  , $this->_(array('labels.ticket'  ,'labels.tickets'  ,2)));
			echo sprintf($li, BASE_URI.'/locations', $this->_(array('labels.location','labels.locations',2)));
			if (Person::isAllowed('people', 'view')) {
				echo sprintf($li, BASE_URI.'/people'   , $this->_(array('labels.person'  ,'labels.people'   ,2)));
			}
			if (Person::isAllowed('tickets', 'add')) {
				echo sprintf($li, BASE_URI.'/tickets/add', $this->_('labels.add_new_ticket'));
			}
			if (Person::isAllowed('reports')) {
				echo sprintf($li, BASE_URI.'/reports', $this->_(array('labels.report', 'labels.reports', 2)));
			}
		?>
	</ul>
</div>
