<?php
/**
 * @copyright 2009-2010 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$return_url = isset($_REQUEST['return_url']) ? $_REQUEST['return_url'] : BASE_URL;

$template = new Template();
$template->blocks[] = new Block('loginForm.inc',array('return_url'=>$return_url));
echo $template->render();
