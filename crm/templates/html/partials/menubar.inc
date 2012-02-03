<div id="menuBar">
	<ul><?php
			echo "
			<li><a href=\"".BASE_URL."/tickets\">Cases</a></li>
			<li><a href=\"".BASE_URL."/locations\">Locations</a></li>
			";
			if (userIsAllowed('people')) {
			echo "
			<li><a href=\"".BASE_URL."/people\">People</a></li>
			";
			}
		?>
	</ul>
</div>
