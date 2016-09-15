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
use Application\Models\Department;
use Application\Models\Ticket;
use Application\Models\TicketHistory;
use Application\Models\TicketTable;
use Application\Models\Search;
use Application\Models\Response;

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
		$query  = $_GET;

		if (isset     ($query['enteredDate']['start'])) {
            if (!empty($query['enteredDate']['start'])) {
                try {  $query['enteredDate']['start'] = new \DateTime($query['enteredDate']['start'].' 00:00:00'); }
                catch (\Exception $e) {
                    unset($query['enteredDate']['start']);
                }
            }
            else {  unset($query['enteredDate']['start']); }
		}
		if (isset     ($query['enteredDate']['end'])) {
            if (!empty($query['enteredDate']['end'])) {
                try {  $query['enteredDate']['end'] = new \DateTime($query['enteredDate']['end'].' 11:59:59'); }
                catch (\Exception $e) {
                    unset($query['enteredDate']['end']);
                }
            }
            else {  unset($query['enteredDate']['end']); }
		}

		$solrObject = $search->query($query, $format=='raw' ? true : false);

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
			$this->template->title = $this->template->_('ticket').' #'.$ticket->getId();
			$this->template->blocks[] = new Block('tickets/ticketInfo.inc', ['ticket'=>$ticket]);
			$this->template->blocks[] = new Block('tickets/slaStatus.inc',  ['ticket'=>$ticket]);
            $this->template->blocks[] = new Block(
                'tickets/history.inc',
                ['history'=>$ticket->getHistory(), 'ticket'=>$ticket]
            );

            if ($ticket->getLocation()) {
                $this->template->blocks['panel-one'][] = new Block('locations/locationInfo.inc', [
                    'location'       => $ticket->getLocation(),
                    'ticket'         => $ticket,
                    'disableButtons' => true
                ]);
            }
		}
		else {
			$_SESSION['errorMessages'][] = new \Exception('noAccessAllowed');
		}
	}

	/**
	 * Displays thumbnails for all image media attached to tickets
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
			$ticket->setReportedByPerson_id($_REQUEST['reportedByPerson_id']);
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

		$this->template->title = $this->template->_('add_ticket');
		if (!empty($_REQUEST['partial']) && $_REQUEST['partial'] === 'tickets/customFieldsForm.inc'
            && $ticket->getCategory_id()) {

            $this->template->blocks[] = new Block('tickets/customFieldsForm.inc', [
                'ticket'   => $ticket,
                'category' => $ticket->getCategory()
            ]);
		}
		else {
            // Display all the forms
            $this->template->blocks[] = new Block('tickets/addTicketForm.inc', [
                'ticket'           => $ticket,
                'currentDepartment'=> $currentDepartment
            ]);
        }
	}

	public function update()
	{
        $ticket = $this->loadTicket($_REQUEST['ticket_id']);

		if (isset($_REQUEST['person_id'])) {
			$ticket->setReportedByPerson_id($_REQUEST['person_id']);
		}

        if (isset($_POST['ticket_id'])) {
            try {
                $ticket->handleUpdate($_POST);
                header('Location: '.$ticket->getURL());
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

		$this->template->title = $this->template->_('update_ticket');
		$this->template->blocks[] = new Block('tickets/updateIssueForm.inc', ['ticket'=>$ticket]);
		$this->template->blocks[] = new Block('tickets/ticketInfo.inc',      ['ticket'=>$ticket,'disableButtons'=>true]);
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
			try { $currentDepartment = new Department($_GET['department_id']); }
			catch (\Exception $e) { }
		}
		if (!isset($currentDepartment)) {
            $person = $ticket->getAssignedPerson();
            if ($person) {
                $d = $person->getDepartment();
                if ($d) { $currentDepartment = $d; }
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
		$this->template->title = $this->template->_('assign_ticket');
		$this->template->blocks[] = new Block(
			'tickets/assignTicketForm.inc',
			['ticket'=>$ticket, 'currentDepartment'=>$currentDepartment]
		);
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
                    $_POST['actionDate'] = \DateTime::createFromFormat(
                        DATE_FORMAT.' '.TIME_FORMAT,
                        $_POST['actionDate']['date'].' '.$_POST['actionDate']['time']
                    );
                    if (!$_POST['actionDate']) { throw new \Exception('invalidDate'); }

                    $history->handleUpdate($_POST);
                    $history->save();
                    $this->redirectToTicketView($ticket);
                }
                catch (\Exception $e) {
                    $_SESSION['errorMessages'][] = $e;
                }
            }

            $this->template->title = $action->getName();
            $this->template->blocks[] = new Block('tickets/actionForm.inc', ['ticketHistory'=>$history]);
        }
		else {
			header('Location: '.BASE_URL.'/tickets');
			exit();
		}
	}

	/**
	 * @param string $status
	 */
	private function changeStatus($status)
	{
		$ticket = $this->loadTicket($_REQUEST['ticket_id']);
		$ticket->setStatus($status);
		$_POST['status'] = $ticket->getStatus();

		if (isset($_POST['ticket_id'])) {
			try {
                // This function will call $ticket->save() internally
                $ticket->handleChangeStatus($_POST);
				$this->redirectToTicketView($ticket);
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		// Display the view
		$this->template->setFilename('tickets');
		$this->template->blocks[] = new Block('tickets/changeStatusForm.inc', ['ticket'=>$ticket]);
	}
	public function close() { $this->template->title = $this->template->_('ticket_close'); $this->changeStatus('closed'); }
	public function open () { $this->template->title = $this->template->_('ticket_open' ); $this->changeStatus('open'  ); }

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

		$_REQUEST['return_url'] = BASE_URL.'/tickets/changeLocation?ticket_id='.$ticket->getId();
		$this->template->title = $this->template->_('change_location');
		$this->template->blocks[] = new Block(
            'locations/findLocationForm.inc',
			['includeExternalResults' => true]
		);
		$this->template->blocks[] = new Block('locations/mapChooser.inc');
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
		$this->template->title = $this->template->_('change_category');
		$this->template->title = 'Change Category';
		$this->template->blocks[] = new Block('tickets/changeCategoryForm.inc', ['ticket'=>$ticket]);
	}

	/**
	 * Saves a response log entry
	 *
	 * This creates a history entry that a staff person communicated
	 * with someone.  Thist action does not actually send any messages.
	 * This is only the logging action.
	 *
	 * @param REQUEST ticket_id
	 */
	public function respond()
	{
        $ticket = $this->loadTicket($_REQUEST['ticket_id']);

		if (isset($_POST['contactMethod_id'])) {
			try {
                $ticket->handleResponse($_POST);
				header('Location: '.$ticket->getURL());
				exit();
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		// Display the view
		$this->template->setFilename('tickets');
		$this->template->title = $this->template->_('add_response');
		$this->template->blocks[] = new Block('tickets/responseForm.inc', ['ticket'=>$ticket]);
	}

	/**
	 * Sends an email to the chosen person
	 */
	public function message()
	{
        $ticket = $this->loadTicket($_REQUEST['ticket_id']);

        if (defined('NOTIFICATIONS_ENABLED') && NOTIFICATIONS_ENABLED) {
            if (isset($_POST['message'])) {
                $template = new Template('email', 'txt');
                $block = new Block('notifications/history.inc', [
                    'ticket'       => $ticket,
                    'userComments' => $_POST['message']
                ]);
                try {
                    $person = new Person($_POST['person_id']);
                    $person->sendNotification($block->render('txt', $template));

                    $history = new TicketHistory();
                    $history->setTicket($ticket);
                    $history->setEnteredByPerson($_SESSION['USER']);
                    $history->setActionPerson($person);
                    $history->setNotes($_POST['message']);
                    $history->setAction(new Action(Action::RESPONDED));
                    $history->save();

                    header('Location: '.$ticket->getURL());
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }

            $this->template->setFilename('tickets');
            $this->template->title = $this->template->_('message_send');
            $this->template->blocks[] = new Block('tickets/messageForm.inc', ['ticket'=>$ticket]);
        }
        else {
            $_SESSION['errorMessages'][] = new \Exception('notificationsDisabled');
        }
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
		$parent = $this->loadTicket($_REQUEST['parent_ticket_id']);
		$child  = $this->loadTicket($_REQUEST[ 'child_ticket_id']);

		if (!empty($_POST['confirm']) && $_POST['confirm']) {
            try {
                $parent->mergeFrom($child);
                $this->redirectToTicketView($parent);
            }
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		// Display the form
		$this->template->setFilename('merging');
		$this->template->title = $this->template->_('merge_tickets');
		$this->template->blocks[] = new Block('tickets/mergeForm.inc', ['parent'=>$parent, 'child'=>$child]);

		$this->template->blocks['left' ][] = new Block('tickets/ticketInfo.inc', ['ticket' =>$parent,'disableButtons'=>true]);
		$this->template->blocks['left' ][] = new Block('tickets/history.inc',    ['history'=>$parent->getHistory(), 'disableComments'=>true]);

		$this->template->blocks['right'][] = new Block('tickets/ticketInfo.inc', ['ticket' =>$child, 'disableButtons'=>true]);
		$this->template->blocks['right'][] = new Block('tickets/history.inc',    ['history'=>$child->getHistory(), 'disableComments'=>true]);
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
}
