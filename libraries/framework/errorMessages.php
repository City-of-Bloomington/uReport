<?php
/**
 * @copyright 2007-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
if (isset($_SESSION['errorMessages'])) {
	$errorBlock = new Block('errorMessages.inc',array('errorMessages'=>$_SESSION['errorMessages']));
	echo $errorBlock->render($this->outputFormat);
	unset($_SESSION['errorMessages']);
}
