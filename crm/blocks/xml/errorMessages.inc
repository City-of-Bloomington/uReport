<?php
/**
 * @copyright 2007-2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->errorMessages
 */
echo "<errors>"
foreach ($this->errorMessages as $e) {
	echo "<error>";
	$error = $e->getMessage();
	if (file_exists(APPLICATION_HOME."/blocks/html/errorMessages/$error.inc")) {
		include APPLICATION_HOME."/blocks/html/errorMessages/$error.inc";
	}
	else {
		echo "<description>$error</description>";
	}
	echo "</error>";
}
echo "</errors>";
