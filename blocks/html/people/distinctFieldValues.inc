<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->results
 */

echo "
<div class=\"distinctFieldValues\">
	<ul>
";
	foreach ($this->results as $value) {
		$value = View::escape($value);
		echo "<li>$value</li>";
	}
echo "
	</ul>
</div>
";