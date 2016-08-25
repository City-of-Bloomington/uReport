<footer>
    <p> Disclaimer: Content submitted to uReport is considered to be a public record
        and may be published by the City as public open data or be subject to public
        records requests. uReport content may be submitted by third parties unaffiliated
        with the City and the City takes no responsibility and disclaims all liability
        for such third party content. Requests submitted by the community are addressed
        on the basis of priority and available resources.
    </p>
	<p>Copyright &#169; 2011-2016 City of Bloomington, Indiana. All rights reserved.</p>
	<p> Powered by <a href="https://github.com/City-of-Bloomington/uReport">uReport</a>
		version <?php include APPLICATION_HOME.'/VERSION'; ?>
	</p>
	<p> This application is free software; you can redistribute it and/or modify
		it under the terms of the
		<a href="http://www.gnu.org/licenses/agpl.html">GNU Affero General Public License</a>
	</p>
</footer>
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
