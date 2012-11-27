<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
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
		catch (Exception $e) {
			$_SESSION['errorMessages'][] = $e;
			header('Location: '.BASE_URL.'/tickets');
			exit();
		}

		$this->template->setFilename('issues');
		$this->template->blocks['ticket-panel'][] = new Block(
			'tickets/ticketInfo.inc',array('ticket'=>$ticket)
		);

		if (userIsAllowed('people', 'view')) {
			$person = $issue->getReportedByPerson();
			if ($person) {
				$this->template->blocks['person-panel'][] = new Block(
					'people/personInfo.inc',
					array('person'=>$person,'disableButtons'=>true)
				);
			}
		}

		$this->template->blocks['issue-panel'][] = new Block(
			'tickets/issueInfo.inc', array('issue'=>$issue)
		);
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
				$ticket = $issue->getTicket();
			}
			// To add new issues, pass in the Ticket to add the issue to
			else {
				if (!empty($_REQUEST['ticket_id'])) {
					$ticket = new Ticket($_REQUEST['ticket_id']);
					$issue = new Issue();
					$issue->setTicket($ticket);
				}
				else { throw new Exception('tickets/unknownTicket'); }
			}
		}
		catch (Exception $e) {
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
				header('Location: '.$ticket->getURL());
				exit();
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		//-------------------------------------------------------------------
		// Display the view
		//-------------------------------------------------------------------
		$this->template->setFilename('tickets');
		$this->template->blocks['ticket-panel'][] = new Block(
			'tickets/ticketInfo.inc',
			array('ticket'=>$ticket,'disableButtons'=>true)
		);
		$this->template->blocks['history-panel'][] = new Block(
			'tickets/history.inc',
			array('history'=>$ticket->getHistory())
		);
		$this->template->blocks['issue-panel'][] = new Block(
			'tickets/updateIssueForm.inc', array('issue'=>$issue, 'ticket'=>$ticket)
		);

		$this->addLocationInfoBlocks($ticket);
	}

	/**
	 * @param GET issue_id
	 */
	public function delete()
	{
		// Load the issue
		try {
			$issue = new Issue($_REQUEST['issue_id']);
			$ticket = $issue->getTicket();
		}
		catch (Exception $e) {
			$_SESSION['errorMessages'][] = $e;
			header('Location: '.BASE_URL);
			exit();
		}

		// Once they've confirmed, go ahead and do the delete
		if (isset($_REQUEST['confirm'])) {
			try {
				$ticket = $issue->getTicket();
				$issue->delete();
				header('Location: '.$ticket->getURL());
				exit();
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				#header('Location: '.$this->return_url);
				print_r($e);
				exit();
			}
		}

		// Display the confirmation form
		$this->template->blocks[] = new Block(
			'confirmForm.inc',
			array('title'=>'Confirm Delete', 'return_url'=>$ticket->getURL())
		);
		$this->template->blocks[] = new Block(
			'tickets/issueInfo.inc',
			array('issue'=>$issue, 'disableButtons'=>true)
		);
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
		catch (Exception $e) {
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
			catch (Exception $e) {
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
			$this->template->blocks['bottom-right'][] = new Block(
				'tickets/ticketList.inc',
				array(
					'ticketList'=>new TicketList(array('location'=>$ticket->getLocation())),
					'title'=>'Other tickets for this location',
					'disableButtons'=>true,
					'filterTicket'=>$ticket
				)
			);
		}
	}
}