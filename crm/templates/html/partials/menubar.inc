<div id="menuBar">
	<ul><?php
			$li = '<li><a href="%s">%s</a></li>';
			echo sprintf($li, BASE_URI.'/tickets'  , $this->translate(array('labels.ticket'  ,'labels.tickets'  ,2)));
			echo sprintf($li, BASE_URI.'/locations', $this->translate(array('labels.location','labels.locations',2)));
			if (userIsAllowed('people', 'view')) {
				echo sprintf($li, BASE_URI.'/people'   , $this->translate(array('labels.person'  ,'labels.people'   ,2)));
			}
			if (userIsAllowed('tickets', 'add')) {
				echo sprintf($li, BASE_URI.'/tickets/add', $this->translate('labels.add_new_ticket'));
			}
			if (userIsAllowed('reports')) {
				echo sprintf($li, BASE_URI.'/reports', $this->translate(array('labels.report', 'labels.reports', 2)));
			}
		?>
	</ul>
</div>
