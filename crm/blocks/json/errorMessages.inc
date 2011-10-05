<?php
/**
 * @copyright 2007-2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->errorMessages
 */
$errors = array();
foreach ($this->errorMessages as $e) {
	ob_start();
	$error = $e->getMessage();
	if (file_exists(APPLICATION_HOME."/blocks/json/errorMessages/$error.inc")) {
		include APPLICATION_HOME."/blocks/json/errorMessages/$error.inc";
	}
	else {
		echo "{\"description\":\"$error\"}";
	}
	$errors[] = ob_get_clean();
}
echo '['.implode(',',$errors).']';
