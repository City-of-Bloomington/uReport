<div id="footer">
	<p>
		<?php
			# Calculate the process time
			$endTime = microtime(1);
			$processTime = $endTime - $startTime;
			echo "<!-- Process Time: $processTime -->";
		?>
	</p>
</div>
