<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
if (!isset($_SESSION['USER'])) {
	header('Location: '.BASE_URL.'/login');
	exit();
}
$template = new Template();
echo $template->render();
