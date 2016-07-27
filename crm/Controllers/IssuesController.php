<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;

use Application\Models\Action;
use Application\Models\Issue;
use Application\Models\TicketHistory;
use Application\Models\Person;
use Application\Models\Response;
use Application\Models\Ticket;
use Application\Models\TicketTable;

use Blossom\Classes\Block;
use Blossom\Classes\Controller;
use Blossom\Classes\Template;

class IssuesController extends Controller
{
	/**
	 * Displays a single issue from a ticket
	 */
	public function index()
	{
		try {
			$issue = new Issue($_GET['issue_id']);
			$ticket = $issue->getTicket();
		}
		catch (\Exception $e) {
			$_SESSION['errorMessages'][] = $e;
			header('Location: '.BASE_URL.'/tickets');
			exit();
		}

		$this->template->setFilename('issues');
		$this->template->blocks[] = new Block('tickets/issueInfo.inc',  ['issue'  => $issue ]);
		$this->template->blocks[] = new Block('tickets/ticketInfo.inc', ['ticket' => $ticket]);
	}

	/**
	 * Handles issue editing
	 *
	 * Choosing a person involves going through a whole person finding process
	 * at a different url.  Once the user has chosen a new person, they will
	 * return here, passing in the person_id they have chosen
	 *
	 * @param REQUEST issue_id   Existing issues are edited by passing in an Issue
	 * @param REQUEST ticket_id  New issues are created by passing in a Ticket
	 * @param REQUEST person_id  The new reportedByPerson
	 */
	public function update()
	{
		//-------------------------------------------------------------------
		// Load all the data that's passed in
		//-------------------------------------------------------------------
		try {
			// To edit existing issues, pass in the issue_id
			if (!empty($_REQUEST['issue_id'])) {
				$issue = new Issue($_REQUEST['issue_id']);
			}
			// To add new issues, pass in the Ticket to add the issue to
			else {
				if (!empty($_REQUEST['ticket_id'])) {
					$issue = new Issue();
					$issue->setTicket_id($_REQUEST['ticket_id']);
				}
				else { throw new \Exception('tickets/unknownTicket'); }
			}
		}
		catch (\Exception $e) {
			$_SESSION['errorMessages'][] = $e;
			header('Location: '.BASE_URL.'/tickets');
			exit();
		}

		if (isset($_REQUEST['person_id'])) {
			$issue->setReportedByPerson_id($_REQUEST['person_id']);
		}

		//-------------------------------------------------------------------
		// Handle any stuff the user posts
		//-------------------------------------------------------------------
		if (isset($_POST['issueType_id'])) {
			$issue->handleUpdate($_POST);
			try {
				$issue->save();

				// Update the search index
				// Make sure the ticket uses the latest issue information
				$ticket = new Ticket($issue->getTicket_id());
				$ticket->updateSearchIndex();

				header('Location: '.$ticket->getURL());
				exit();
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		//-------------------------------------------------------------------
		// Display the view
		//-------------------------------------------------------------------
		$ticket = $issue->getTicket();

		$this->template->setFilename('tickets');
		$this->template->blocks[] = new Block('tickets/updateIssueForm.inc', ['issue'=>$issue, 'ticket'=>$ticket]);
		$this->template->blocks[] = new Block('tickets/ticketInfo.inc', ['ticket'=>$ticket,'disableButtons'=>true]);
	}

	/**
	 * @param REQUEST issue_id
	 */
	public function respond()
	{
		// Load the issue
		try {
			$issue = new Issue($_REQUEST['issue_id']);
			$ticket = $issue->getTicket();
		}
		catch (\Exception $e) {
			$_SESSION['errorMessages'][] = $e;
			header('Location: '.BASE_URL.'/tickets');
			exit();
		}

		// Handle what the user posts
		if (isset($_POST['contactMethod_id'])) {
			$r = new Response();
			$r->handleUpdate($_POST);
			try {
				$r->save();
				header('Location: '.$ticket->getURL());
				exit();
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		// Display the view
		$this->template->setFilename('tickets');
		$this->template->blocks['ticket-panel'][] = new Block(
			'tickets/ticketInfo.inc',
			array('ticket'=>$ticket)
		);
		$this->template->blocks['history-panel'][] = new Block(
			'tickets/history.inc',
			array('history'=>$ticket->getHistory())
		);
		$this->template->blocks['issue-panel'][] = new Block(
			'tickets/responseForm.inc', array('issue'=>$issue)
		);

		$this->addLocationInfoBlocks($ticket);
	}

	/**
	 * @param Ticket $ticket
	 */
	private function addLocationInfoBlocks(Ticket $ticket)
	{
		if ($ticket->getLocation()) {
			$this->template->blocks['bottom-left'][] = new Block(
				'locations/locationInfo.inc',
				array('location'=>$ticket->getLocation(),'disableButtons'=>true)
			);
			$table = new TicketTable();
			$this->template->blocks['bottom-right'][] = new Block(
				'tickets/ticketList.inc',
				array(
					'ticketList'    => $table->find(['location'=>$ticket->getLocation()]),
					'title'         => 'Other tickets for this location',
					'disableButtons'=> true,
					'filterTicket'  => $ticket
				)
			);
		}
	}
}
