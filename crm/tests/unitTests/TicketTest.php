<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
require_once './configuration.inc';
class TicketTest extends PHPUnit_Framework_TestCase
{
	private $data = array(
		'location'=>'Test Location',
		'city'    =>'Bloomington',
		'state'   =>'IN',
		'zip'     =>'470404'
	);

	public function testHandleUpdate()
	{
		$ticket = new Ticket();
		$ticket->handleUpdate(array(
			'location'=> $this->data['location'],
			'city'    => $this->data['city'],
			'state'   => $this->data['state'],
			'zip'     => $this->data['zip']
		));
		$this->assertEquals($this->data['location'], $ticket->getLocation());
		$this->assertEquals($this->data['city'],     $ticket->getCity());
		$this->assertEquals($this->data['state'],    $ticket->getState());
		$this->assertEquals($this->data['zip'],      $ticket->getZip());
	}
}
