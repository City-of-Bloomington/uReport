<div id="footer">
	<p>Copyright &#169; 2011-2013 City of Bloomington, Indiana. All rights reserved.</p>
	<p> Powered by <a href="https://github.com/City-of-Bloomington/uReport">uReport</a>
		version <?php include APPLICATION_HOME.'/VERSION'; ?>
	</p>
	<p> This application is free software; you can redistribute it and/or modify
		it under the terms of the
		<a href="http://www.gnu.org/licenses/agpl.html">GNU Affero General Public License</a>
	</p>
	<p>
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
