<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
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
		catch (Exception $e) {
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
		$this->template->setFilename('search');

		$search = new Search();
		$solrObject = $search->query($_GET);

		$this->template->blocks['left'][] = new Block(
			'tickets/searchForm.inc',
			array('solrObject'=>$solrObject)
		);
		$this->template->blocks['right'][] = new Block(
			'tickets/searchParameters.inc',
			array('solrObject'=>$solrObject)
		);
		$this->template->blocks['right'][] = new Block(
			'tickets/searchResults.inc',
			array('solrObject'=>$solrObject)
		);
	}

	/**
	 * @param GET ticket_id
	 */
	public function view()
	{
		$ticket = $this->loadTicket($_GET['ticket_id']);

		if ($ticket->allowsDisplay(isset($_SESSION['USER']) ? $_SESSION['USER'] : 'anonymous')) {
			$this->template->setFilename('tickets');
			$this->template->blocks['ticket-panel'][] = new Block(
				'tickets/ticketInfo.inc',
				array('ticket'=>$ticket)
			);
			$this->template->blocks['ticket-panel'][] = new Block(
				'tickets/slaStatus.inc',
				array('ticket'=>$ticket)
			);
			if (userIsAllowed('tickets', 'update') && $ticket->getStatus()!='closed') {
				$this->template->blocks['history-panel'][] = new Block(
					'tickets/actionForm.inc',
					array('ticket'=>$ticket)
				);
			}
			$this->addStandardInfoBlocks($ticket);
		}
		else {
			$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
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
				$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
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
		if (isset($_GET['department_id'])) {
			try {
				$currentDepartment = new Department($_GET['department_id']);
			}
			catch (Exception $e) {
				// Ignore any bad departments passed in
			}
		}
		// If they haven't chosen a department, start by assigning
		// the ticket to the current User, and use the current user's department
		if (!isset($currentDepartment)) {
			$ticket->setAssignedPerson($_SESSION['USER']);
			$currentDepartment = $_SESSION['USER']->getDepartment();
		}

		// Process the ticket form when it's posted
		if (isset($_POST['category_id'])) {
			try {
				$ticket->handleAdd($_POST); // Calls save as needed - no need to save() again
				$this->redirectToTicketView($ticket);
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		// Display all the forms
		$this->template->setFilename('ticketCreation');
		$this->template->blocks['right-top'][] = new Block(
			'tickets/chooseLocation.inc', array('ticket'=>$ticket)
		);
		$this->template->blocks['right-bottom'][] = new Block(
			'tickets/chooseReportedByPerson.inc', array('issue'=>$issue)
		);
		$this->template->blocks['left'][] = new Block(
			'tickets/addTicketForm.inc',
			array(
				'ticket'=>$ticket,
				'issue'=>$issue,
				'currentDepartment'=>$currentDepartment
			)
		);
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
			array('title'=>'Confirm Delete','return_url'=>$ticket->getURL())
		);
		$this->template->blocks[] = new Block(
			'tickets/ticketInfo.inc',
			array('ticket'=>$ticket,'disableButtons'=>true)
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
			catch (Exception $e) {
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
				$history->setAction(new Action('assignment'));
				$history->setEnteredByPerson($_SESSION['USER']);
				$history->setActionPerson($ticket->getAssignedPerson());
				$history->setNotes($_REQUEST['notes']);
				$history->save();

				$history->sendNotification($ticket);

				$this->redirectToTicketView($ticket);
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		// Display the view
		$this->template->setFilename('tickets');
		$this->template->blocks['ticket-panel'][] = new Block(
			'departments/chooseDepartmentForm.inc',
			array('currentDepartment'=>$currentDepartment)
		);
		$this->template->blocks['ticket-panel'][] = new Block(
			'tickets/assignTicketForm.inc',
			array('ticket'=>$ticket,'currentDepartment'=>$currentDepartment)
		);
		$this->addStandardInfoBlocks($ticket);
	}

	/**
	 * @param REQUEST ticket_id
	 * @param REQUEST person_id
	 */
	public function refer()
	{
		$ticket = $this->loadTicket($_REQUEST['ticket_id']);
		if (isset($_REQUEST['person_id'])) {
			try {
				$person = new Person($_REQUEST['person_id']);
			}
			catch (Exception $e) {
			}
		}

		// Handle any stuff the user posts
		if (isset($_POST['referredPerson_id'])) {
			try {
				$ticket->setReferredPerson_id($_POST['referredPerson_id']);
				$ticket->save();

				// add a record to ticket history
				$history = new TicketHistory();
				$history->setTicket($ticket);
				$history->setAction(new Action('referral'));
				$history->setEnteredByPerson($_SESSION['USER']);
				$history->setActionPerson($ticket->getReferredPerson());
				$history->setNotes($_POST['notes']);
				$history->save();

				$this->redirectToTicketView($ticket);
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		// Display the view
		$this->template->setFilename('tickets');
		if (isset($person)) {
			$this->template->blocks['ticket-panel'][] = new Block(
				'tickets/referTicketForm.inc',
				array('ticket'=>$ticket,'person'=>$person)
			);
		}
		else {
			$_REQUEST['return_url'] = BASE_URL.'/tickets/refer?ticket_id='.$ticket->getId();
			$this->template->blocks['ticket-panel'][] = new Block('people/searchForm.inc');
		}

		$this->addStandardInfoBlocks($ticket);
	}

	/**
	 * @param POST ticket_id
	 */
	public function recordAction()
	{
		if (!empty($_POST['ticket_id'])) {
			$ticket = $this->loadTicket($_POST['ticket_id']);

			$history = new TicketHistory();
			try {
				$history->handleUpdate($_POST);
				$history->save();
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
			$this->redirectToTicketView($ticket);
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

		if (isset($_POST['status'])) {
			try {
				$substatus_id = !empty($_POST['substatus_id']) ? $_POST['substatus_id'] : null;
				$ticket->setStatus($_POST['status'], $substatus_id);
				$ticket->save();

				try {
					$action = new Action($_POST['status']);

					// add a record to ticket history
					$history = new TicketHistory();
					$history->setTicket($ticket);
					$history->setAction($action);
					$history->setNotes($_POST['notes']);
					$history->save();
				}
				catch (Exception $e) {
					// If the status doesn't have an action, don't record a history entry
				}

				$this->redirectToTicketView($ticket);
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		// Display the view
		$this->template->setFilename('tickets');
		$this->template->blocks['ticket-panel'][] = new Block(
			'tickets/changeStatusForm.inc',
			array('ticket'=>$ticket)
		);

		$this->addStandardInfoBlocks($ticket);
	}

	/**
	 * @param REQUEST ticket_id
	 * @param REQUEST location
	 */
	public function changeLocation()
	{
		$ticket = $this->loadTicket($_REQUEST['ticket_id']);

		// Once the user has chosen a location, they'll pass it in here
		if (isset($_REQUEST['location']) && $_REQUEST['location']) {
			$ticket->clearAddressServiceData();
			$ticket->setLocation($_REQUEST['location']);
			$ticket->setAddressServiceData(AddressService::getLocationData($ticket->getLocation()));
			try {
				$ticket->save();
				$this->redirectToTicketView($ticket);
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$_REQUEST['return_url'] = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
		$this->template->setFilename('tickets');
		$this->template->blocks['ticket-panel'][] = new Block(
			'locations/findLocationForm.inc',
			array('includeExternalResults'=>true)
		);

		$this->addStandardInfoBlocks($ticket);
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
				$ticket->setCategory_id($_REQUEST['category_id']);
				$ticket->save();
				$this->redirectToTicketView($ticket);
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		// Display the view
		$this->template->setFilename('tickets');
		$this->template->title = 'Change Category';
		$this->template->blocks['ticket-panel'][] = new Block(
			'tickets/changeCategoryForm.inc',
			array('ticket'=>$ticket)
		);

		$this->addStandardInfoBlocks($ticket);
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
			catch (Exception $e) {
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
			$return_url = new URL(BASE_URL.'/callback');
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
		$this->template->blocks['history-panel'][] = new Block(
			'tickets/history.inc',
			array('history'=>$ticket->getHistory())
		);

		$this->template->blocks['issue-panel'][] = new Block(
			'tickets/issueList.inc',
			array(
				'issueList'     => $ticket->getIssues(),
				'ticket'        => $ticket,
				'disableButtons'=> $ticket->getStatus()=='closed'
			)
		);
		if ($ticket->getLocation()) {
			$locationBlocks = array('locationInfo', 'masterAddressData', 'locationPeople');
			foreach ($locationBlocks as $b) {
				$this->template->blocks['bottom-left'][] = new Block(
					"locations/$b.inc",
					array('location'=>$ticket->getLocation(), 'disableButtons'=>true)
				);
			}

			$this->template->blocks['bottom-right'][] = new Block(
				'tickets/ticketLocationInfo.inc',
				array('ticket'=>$ticket)
			);

			$ticketList = new TicketList(array('location'=>$ticket->getLocation()));
			if (count($ticketList) > 1) {
				$this->template->blocks['bottom-left'][] = new Block(
					'tickets/ticketList.inc',
					array(
						'ticketList'    => $ticketList,
						'title'         => 'Other cases for this location',
						'filterTicket'  => $ticket,
						'disableButtons'=> true
					)
				);
			}
		}
	}
}