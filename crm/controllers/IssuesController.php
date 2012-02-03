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
			$ticket = new Ticket($_GET['ticket_id']);
			$issues = $ticket->getIssues();
		}
		catch (Exception $e) {
			$_SESSION['errorMessages'][] = $e;
			header('Location: '.BASE_URL.'/tickets');
			exit();
		}

		if (!isset($issues[$_GET['index']])) {
			$_SESSION['errorMessages'][] = new Exception('tickets/unknownIssue');
			header('Location: '.$ticket->getURL());
			exit();
		}

		$issue = $issues[$_GET['index']];

		$this->template->setFilename('issues');
		$this->template->blocks['ticket-panel'][] = new Block(
			'tickets/ticketInfo.inc',array('ticket'=>$ticket)
		);

		$person = $issue->getPersonObject('reportedByPerson');
		if ($person) {
			$this->template->blocks['person-panel'][] = new Block(
				'people/personInfo.inc',
				array('person'=>$person,'disableButtons'=>true)
			);
		}
		$this->template->blocks['issue-panel'][] = new Block(
			'tickets/issueInfo.inc',
			array('ticket'=>$ticket,'issue'=>$issue,'index'=>$_GET['index'])
		);
	}

	/**
	 * Handles issue editing
	 *
	 * Choosing a person involves going through a whole person finding process
	 * at a different url.  Once the user has chosen a new person, they will
	 * return here, passing in the person_id they have chosen
	 *
	 * @param REQUEST ticket_id
	 * @param REQUEST index The index number of the issue
	 * @param REQUEST person_id The new person to apply to the issue
	 */
	public function update()
	{
		//-------------------------------------------------------------------
		// Load all the data that's passed in
		//-------------------------------------------------------------------
		try {
			$ticket = new Ticket($_REQUEST['ticket_id']);
			$issues = $ticket->getIssues();
			if (isset($_REQUEST['index']) && array_key_exists($_REQUEST['index'],$issues)) {
				$issue = $issues[$_REQUEST['index']];
				$index = (int)$_REQUEST['index'];
			}
			else {
				$issue = new Issue();
				$index = null;
			}
		}
		catch (Exception $e) {
			$_SESSION['errorMessages'][] = $e;
			header('Location: '.BASE_URL.'/tickets');
			exit();
		}

		if (isset($_REQUEST['person_id'])) {
			$issue->setReportedByPerson($_REQUEST['person_id']);
		}

		//-------------------------------------------------------------------
		// Handle any stuff the user posts
		//-------------------------------------------------------------------
		if (isset($_POST['issue'])) {
			$issue->set($_POST['issue']);
			$ticket->updateIssues($issue,$index);

			try {
				$ticket->save();
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
			'tickets/updateIssueForm.inc',
			array('ticket'=>$ticket,'index'=>$index,'issue'=>$issue)
		);
		$this->template->blocks['location-panel'][] = new Block(
			'locations/locationInfo.inc',
			array('location'=>$ticket->getLocation())
		);
		if ($ticket->getLocation()) {
			$this->template->blocks['location-panel'][] = new Block(
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

	/**
	 * @param GET ticket_id
	 * @param GET index The index of the issue inside the ticket
	 */
	public function delete()
	{
		// Load the ticket
		try {
			$ticket = new Ticket($_REQUEST['ticket_id']);
			$issues = $ticket->getIssues();
		}
		catch (Exception $e) {
			$_SESSION['errorMessages'][] = $e;
			header('Location: '.BASE_URL);
			exit();
		}

		if (!isset($issues[$_GET['index']])) {
			$_SESSION['errorMessages'][] = new Exception('tickets/unknownIssue');
			header('Location: '.$ticket->getURL());
			exit();
		}
		$issue = $issues[$_GET['index']];

		// Once they've confirmed, go ahead and do the delete
		if (isset($_REQUEST['confirm'])) {
			try {
				$ticket->removeIssue($_GET['index']);
				$ticket->save();
				header('Location: '.$ticket->getURL());
				exit();
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				header('Location: '.$this->return_url);
				exit();
			}
		}

		// Display the confirmation form
		$this->template->blocks[] = new Block(
			'confirmForm.inc',
			array('title'=>'Confirm Delete','return_url'=>$ticket->getURL())
		);
		$this->template->blocks[] = new Block(
			'tickets/issueInfo.inc',
			array('ticket'=>$ticket,'issue'=>$issue,'index'=>$_GET['index'],'disableButtons'=>true)
		);
	}

	/**
	 * @param REQUEST ticket_id
	 * @param REQUEST index The index of the issue
	 */
	public function respond()
	{
		// Load the ticket
		try {
			$ticket = new Ticket($_REQUEST['ticket_id']);
			$index = (int)$_REQUEST['index'];
			$issue = $ticket->getIssue($index);
			if (!$issue) {
				throw new Exception('unknownIssue');
			}
		}
		catch (Exception $e) {
			$_SESSION['errorMessages'][] = $e;
			header('Location: '.BASE_URL);
			exit();
		}

		// Handle what the user posts
		if (isset($_POST['contactMethod'])) {
			$response = new Response();
			$response->setPerson($_SESSION['USER']);
			$response->setContactMethod($_POST['contactMethod']);
			$response->setNotes($_POST['notes']);

			try {
				$ticket->addResponse($index,$response);
				$ticket->save();
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
			'tickets/responseForm.inc',
			array('ticket'=>$ticket,'index'=>$index)
		);
		if ($ticket->getLocation()) {
			$this->template->blocks['location-panel'][] = new Block(
				'locations/locationInfo.inc',
				array('location'=>$ticket->getLocation())
			);
			$this->template->blocks['location-panel'][] = new Block(
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