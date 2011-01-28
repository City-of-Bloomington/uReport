<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET ticket_id
 */
$ticket = new Ticket($_GET['ticket_id']);

$template = new Template();
$template->blocks[] = new Block('tickets/ticketInfoPanel.inc',array('ticket'=>$ticket));
$template->blocks[] = new Block('locations/locationPanel.inc',array('location'=>$ticket->getLocation()));
echo $template->render();