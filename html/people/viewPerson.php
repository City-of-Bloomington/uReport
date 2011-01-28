<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET person_id
 */
$person = new Person($_GET['person_id']);

$template = new Template();
$template->title = $person->getFullname();
$template->blocks[] = new Block('people/personPanel.inc',array('person'=>$person));
echo $template->render();
