<div id="menuBar">
	<ul><?php
			echo "
			<li><a href=\"".BASE_URI."/tickets\">Cases</a></li>
			<li><a href=\"".BASE_URI."/locations\">Locations</a></li>
			";
			if (userIsAllowed('people')) {
				echo "<li><a href=\"".BASE_URI."/people\">People</a></li>";
			}
			if (userIsAllowed('tickets', 'add')) {
				echo "<li><a href=\"".BASE_URI."/tickets/add\">New Case</a></li>";
			}
		?>
	</ul>
</div>
