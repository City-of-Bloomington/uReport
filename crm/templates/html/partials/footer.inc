<div id="footer">
	<p><?php
			# Calculate the process time
			global $startTime;
			$endTime = microtime(1);
			$processTime = $endTime - $startTime;
			echo "<!-- Process Time: $processTime -->";
		?>
	</p>
</div>
<?php
	if (isset($this->assets['scripts'])) {
		foreach ($this->assets['scripts'] as $url) {
			echo "<script type=\"text/javascript\" src=\"$url\"></script>\n";
		}
	}
	if (isset($this->assets['stylesheets'])) {
		foreach ($this->assets['stylesheets'] as $url) {
			echo "<link rel=\"stylesheet\" href=\"$url\" />\n";
		}
	}
?>