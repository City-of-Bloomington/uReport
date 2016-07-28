<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;

use Application\Models\Action;
use Application\Models\AddressService;
use Application\Models\Person;
use Application\Models\Category;
use Application\Models\Issue;
use Application\Models\Department;
use Application\Models\Ticket;
use Application\Models\TicketHistory;
use Application\Models\TicketTable;
use Application\Models\Search;

use Blossom\Classes\Block;
use Blossom\Classes\Controller;
use Blossom\Classes\Template;
use Blossom\Classes\Url;

class TicketsController extends Controller
{
	/**
	 * @param string $id
	 * @return Ticket
	 */
	private function loadTicket($id)
	{
		try {
			$ticket = new Ticket($id);
			return $ticket;
		}
		catch (\Exception $e) {
			$_SESSION['errorMessages'][] = $e;
			header('Location: '.BASE_URL.'/tickets');
			exit();
		}
	}


	/**
	 * Provides ticket searching
	 */
	public function index()
	{
		$format = isset($_GET['resultFormat']) ? trim($_GET['resultFormat']) : '';

		if ($format == 'raw'
			&& $this->template->outputFormat=='html'
			&& Person::isAllowed('tickets', 'print')) {
			$this->template->setFilename('print');
		}
		else {
			$this->template->setFilename('search');
		}

		$search = new Search();
		$solrObject = $search->query($_GET, $format=='raw' ? true : false);

		$resultBlock = ($format == 'map') ? 'searchResultsMap.inc' : 'searchResults.inc';
		$this->template->blocks['panel-one'][] = new Block('tickets/searchForm.inc', ['solrObject'=>$solrObject]);
		$this->template->blocks[]              = new Block("tickets/$resultBlock",   ['solrObject'=>$solrObject]);
	}

	/**
	 * @param GET ticket_id
	 */
	public function view()
	{
		$ticket = $this->loadTicket($_GET['ticket_id']);

		if ($ticket->allowsDisplay(isset($_SESSION['USER']) ? $_SESSION['USER'] : null)) {
			$this->template->setFilename('tickets');
			$this->template->blocks[] = new Block('tickets/ticketInfo.inc', ['ticket'=>$ticket]);
			$this->template->blocks[] = new Block('tickets/slaStatus.inc',  ['ticket'=>$ticket]);
			$this->addStandardInfoBlocks($ticket);
		}
		else {
			$_SESSION['errorMessages'][] = new \Exception('noAccessAllowed');
		}
	}

	/**
	 * Displays thumbnails for all image media attached to issues
	 *
	 * @param GET ticket_id
	 */
	public function thumbnails()
	{
		$ticket = $this->loadTicket($_GET['ticket_id']);
		if ($ticket->allowsDisplay(isset($_SESSION['USER']) ? $_SESSION['USER'] : null)) {
			$this->template->blocks[] = new Block('tickets/thumbnails.inc', ['ticket'=>$ticket]);
		}
		else {
			$_SESSION['errorMessages'][] = new \Exception('noAccessAllowed');
		}
	}

	/**
	 *
	 */
	public function add()
	{
		$ticket = new Ticket();
		$issue  = new Issue();

		// Categories are required before starting the process
		// Handle any Category choice passed in
		if (!empty($_REQUEST['category_id'])) {
			$category = new Category($_REQUEST['category_id']);
			if ($category->allowsPosting($_SESSION['USER'])) {
				$ticket->setCategory($category);
			}
			else {
				$_SESSION['errorMessages'][] = new \Exception('noAccessAllowed');
				header('Location: '.BASE_URL);
				exit();
			}
		}

		// Handle any Location choice passed in
		if (!empty($_GET['location'])) {
			$ticket->setLocation($_GET['location']);
			$ticket->setAddressServiceData(AddressService::getLocationData($ticket->getLocation()));
		}

		// Handle any Person choice passed in
		if (!empty($_REQUEST['reportedByPerson_id'])) {
			$issue->setReportedByPerson_id($_REQUEST['reportedByPerson_id']);
		}

		// Handle any Department choice passed in
		// Choosing a department here will cause the assignment form
		// to pre-select that department's defaultPerson
		$currentDepartment = null;
		if (isset($_GET['department_id'])) {
			try {
				$currentDepartment = new Department($_GET['department_id']);
			}
			catch (\Exception $e) {
				// Ignore any bad departments passed in
			}
		}
		// If they haven't chosen a department, start by assigning
		// the ticket to the current User, and use the current user's department
		if (!isset($currentDepartment)) {
			$ticket->setAssignedPerson($_SESSION['USER']);

			if ($_SESSION['USER']->getDepartment()) {
				$currentDepartment = $_SESSION['USER']->getDepartment();
			}
		}

		// Process the ticket form when it's posted
		if (isset($_POST['category_id'])) {
			try {
				$ticket->handleAdd($_POST); // Calls save as needed - no need to save() again
				$this->redirectToTicketView($ticket);
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		// Display all the forms
		$this->template->setFilename('ticketCreation');
		$this->template->blocks[] = new Block('tickets/addTicketForm.inc', [
            'ticket'=>$ticket,
            'issue'=>$issue,
            'currentDepartment'=>$currentDepartment
        ]);
	}

	/**
	 * @param REQUEST ticket_id
	 * @param REQUEST confirm
	 */
	public function delete()
	{
		$ticket = $this->loadTicket($_REQUEST['ticket_id']);

		if (isset($_REQUEST['confirm'])) {
			$ticket->delete();
			header('Location: '.BASE_URL.'/tickets');
			exit();
		}

		$this->template->blocks[] = new Block(
			'confirmForm.inc',
			['title'=>'Confirm Delete', 'return_url'=>$ticket->getURL()]
		);
		$this->template->blocks[] = new Block(
			'tickets/ticketInfo.inc',
			['ticket'=>$ticket, 'disableButtons'=>true]
		);
	}

	/**
	 * @param REQUEST ticket_id
	 * @param GET department_id
	 */
	public function assign()
	{
		$ticket = $this->loadTicket($_REQUEST['ticket_id']);

		// Handle any Department choice passed in
		if (isset($_GET['department_id'])) {
			try {
				$currentDepartment = new Department($_GET['department_id']);
			}
			catch (\Exception $e) {
			}
		}
		if (!isset($currentDepartment)) {
			$currentDepartment = $_SESSION['USER']->getDepartment();
		}

		// Handle any stuff the user posts
		if (isset($_REQUEST['assignedPerson_id'])) {
			try {
				$ticket->setAssignedPerson_id($_REQUEST['assignedPerson_id']);
				$ticket->save();

				// add a record to ticket history
				$history = new TicketHistory();
				$history->setTicket($ticket);
				$history->setAction(new Action(Action::ASSIGNED));
				$history->setEnteredByPerson($_SESSION['USER']);
				$history->setActionPerson($ticket->getAssignedPerson());
				$history->setNotes($_REQUEST['notes']);
				$history->save();

				$this->redirectToTicketView($ticket);
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		// Display the view
		$this->template->setFilename('tickets');
		$this->template->blocks[] = new Block(
			'tickets/assignTicketForm.inc',
			['ticket'=>$ticket, 'currentDepartment'=>$currentDepartment]
		);
		#$this->addStandardInfoBlocks($ticket);
	}

	/**
	 * @param POST ticket_id
	 */
	public function recordAction()
	{
        if (!empty($_REQUEST['ticket_id'])) {
            $ticket = $this->loadTicket($_REQUEST['ticket_id']);
        }
        if (!empty($_REQUEST['action_id'])) {
            try { $action = new Action($_REQUEST['action_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (isset($ticket) && isset($action)) {
            $history = new TicketHistory();
            $history->setTicket($ticket);
            $history->setAction($action);

            if (isset($_POST['ticket_id'])) {
                try {
                    $history->handleUpdate($_POST);
                    $history->save();
                    $this->redirectToTicketView($ticket);
                }
                catch (\Exception $e) {
                    $_SESSION['errorMessages'][] = $e;
                }
            }

            $this->template->blocks[] = new Block('tickets/actionForm.inc', ['ticketHistory'=>$history]);
        }
		else {
			header('Location: '.BASE_URL.'/tickets');
			exit();
		}
	}

	/**
	 * @param REQUEST ticket_id
	 */
	public function changeStatus()
	{
		$ticket = $this->loadTicket($_REQUEST['ticket_id']);
		if (!empty($_GET['status'])) {
            $ticket->setStatus($_GET['status'] === 'closed' ? 'closed' : 'open');
		}

		if (isset($_POST['status'])) {
			try {
                // This function will call $ticket->save() internally
                $ticket->handleChangeStatus($_POST);

                // Display an alert, reminding them to respond to any citizens
                #$citizens = $ticket->getReportedByPeople();
                #if (count($citizens)) {
                #    $_SESSION['errorMessages'][] = new \Exception('tickets/closingResponseReminder');
                #}

				$this->redirectToTicketView($ticket);
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		// Display the view
		$this->template->setFilename('tickets');
		$this->template->blocks[] = new Block('tickets/changeStatusForm.inc', ['ticket'=>$ticket]);
		$this->template->blocks[] = new Block('tickets/responseReminder.inc', ['ticket'=>$ticket]);

		#$this->addStandardInfoBlocks($ticket);
	}

	/**
	 * @param REQUEST ticket_id
	 * @param REQUEST location
	 */
	public function changeLocation()
	{
		$ticket = $this->loadTicket($_REQUEST['ticket_id']);

		// Once the user has chosen a location, they'll pass it in here
		if (!empty($_REQUEST['location'])) {
			try {
                $ticket->handleChangeLocation($_REQUEST);
				$this->redirectToTicketView($ticket);
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$_REQUEST['return_url'] = new Url($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
		$this->template->setFilename('locations');
		$this->template->blocks['panel-one'][] = new Block(
            'locations/findLocationForm.inc',
			['includeExternalResults' => true]
		);
		$this->template->blocks['panel-two'][] = new Block('locations/mapChooser.inc');

		#$this->addStandardInfoBlocks($ticket);
	}

	/**
	 * @param REQUEST ticket_id
	 * @param REQUEST category_id
	 */
	public function changeCategory()
	{
		$ticket = $this->loadTicket($_REQUEST['ticket_id']);

		if (isset($_REQUEST['category_id'])) {
			try {
                $ticket->handleChangeCategory($_REQUEST);
				$this->redirectToTicketView($ticket);
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		// Display the view
		$this->template->setFilename('tickets');
		$this->template->title = 'Change Category';
		$this->template->blocks[] = new Block('tickets/changeCategoryForm.inc', ['ticket'=>$ticket]);
		#$this->addStandardInfoBlocks($ticket);
	}

	/**
	 * Copies all data from one ticket to another, then deletes the empty ticket
	 *
	 * @param GET ticket_id_a
	 * @param GET ticket_id_b
	 */
	public function merge()
	{
		// Load the two tickets
		$ticketA = $this->loadTicket($_REQUEST['ticket_id_a']);
		$ticketB = $this->loadTicket($_REQUEST['ticket_id_b']);

		// When the user chooses a target, merge the other ticket into the target
		if (isset($_POST['targetTicket'])) {
			try {
				if ($_POST['targetTicket']=='a') {
					$ticketA->mergeFrom($ticketB);
					$targetTicket = $ticketA;
				}
				else {
					$ticketB->mergeFrom($ticketA);
					$targetTicket = $ticketB;
				}

				$this->redirectToTicketView($targetTicket);
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		// Display the form
		$this->template->setFilename('merging');
		$this->template->blocks[] = new Block(
			'tickets/mergeForm.inc',
			array('ticketA'=>$ticketA,'ticketB'=>$ticketB)
		);

		$this->template->blocks['left'][] = new Block(
			'tickets/ticketInfo.inc',
			array('ticket'=>$ticketA,'disableButtons'=>true)
		);
		$this->template->blocks['left'][] = new Block(
			'tickets/history.inc',
			array('history'=>$ticketA->getHistory(),'disableComments'=>true)
		);
		$this->template->blocks['left'][] = new Block(
			'tickets/issueList.inc',
			array(
				'issueList'=>$ticketA->getIssues(),
				'ticket'=>$ticketA,
				'disableButtons'=>true,
				'disableComments'=>true
			)
		);

		$this->template->blocks['right'][] = new Block(
			'tickets/ticketInfo.inc',
			array('ticket'=>$ticketB,'disableButtons'=>true)
		);
		$this->template->blocks['right'][] = new Block(
			'tickets/history.inc',
			array('history'=>$ticketB->getHistory(),'disableComments'=>true)
		);
		$this->template->blocks['right'][] = new Block(
			'tickets/issueList.inc',
			array(
				'issueList'=>$ticketB->getIssues(),
				'ticket'=>$ticketB,
				'disableButtons'=>true,
				'disableComments'=>true
			)
		);
	}

	/**
	 * @param Ticket $ticket
	 */
	private function redirectToTicketView(Ticket $ticket)
	{
		if (isset($_REQUEST['callback'])) {
			$return_url = new Url(BASE_URL.'/callback');
			$return_url->callback = $_REQUEST['callback'];
		}
		else {
			$return_url = $ticket->getURL();
		}
		header("Location: $return_url");
		exit();
	}

	/**
	 * @param Ticket $ticket
	 */
	private function addStandardInfoBlocks(Ticket $ticket)
	{
		$this->template->blocks[] = new Block(
			'tickets/history.inc',
			['history'=>$ticket->getHistory(), 'ticket'=>$ticket]
		);

		#$this->template->blocks['issue-panel'][] = new Block(
		#	'tickets/issueList.inc',
		#	array(
		#		'issueList'     => $ticket->getIssues(),
		#		'ticket'        => $ticket,
		#		'disableButtons'=> $ticket->getStatus()=='closed'
		#	)
		#);
		if ($ticket->getLocation()) {
            $this->template->blocks['panel-one'][] = new Block('locations/locationInfo.inc', [
                'location'       => $ticket->getLocation(),
                'ticket'         => $ticket,
                'disableButtons' => true
            ]);
		}
	}
}
