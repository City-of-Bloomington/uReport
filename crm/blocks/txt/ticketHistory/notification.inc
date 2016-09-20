<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Ticket $this->ticket
 * @param string $this->actionDescription
 * @param string $this->autoResponse
 * @param string $this->userComments
 */
$message = '';
$message.= $this->ticket            ? $this->ticket->getURL() ."\n\n" : '';
$message.= $this->actionDescription ? $this->actionDescription."\n\n" : '';
$message.= $this->autoResponse      ? $this->autoResponse     ."\n\n" : '';
$message.= $this->userComments      ? $this->userComments     ."\n\n" : '';
echo $message;
