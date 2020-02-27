<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param array $this->results
 */
echo "
<div class=\"distinctFieldValues\">
	<ul>
";
	foreach ($this->results as $value) {
		$value = self::escape($value);
		echo "<li>$value</li>";
	}
echo "
	</ul>
</div>
";
