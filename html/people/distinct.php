<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET field
 * @param GET query
 */
$results = Person::getDistinct($_GET['field'],$_GET['query']);

$template = (isset($_GET['format'])) ? new Template('default',$_GET['format']) : new Template();
$template->blocks[] = new Block('people/distinctFieldValues.inc',array('results'=>$results));
echo $template->render();