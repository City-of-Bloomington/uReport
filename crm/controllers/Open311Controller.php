<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Open311Controller extends Controller
{
	private $person;

	public function __construct(Template $template)
	{
		parent::__construct($template);
		$this->template->setFilename('open311');
		$this->person = isset($_SESSION['USER']) ? $_SESSION['USER'] : 'anonymous';
	}

	public function index()
	{
	}

	public function discovery()
	{
		$this->template->blocks[] = new Block('open311/discovery.inc');
	}

	/**
	 * @param REQUEST service_code
	 */
	public function services()
	{
		// If a service_id is provided, they want the service info
		if (isset($_REQUEST['service_code'])) {
			try {
				$category = new Category($_REQUEST['service_code']);
				if ($category->allowsPosting($this->person)) {
					$this->template->blocks[] = new Block('open311/serviceInfo.inc',array('category'=>$category));
				}
				else {
					// Not allowed to post to this category
					header('HTTP/1.0 403 Forbidden',true,403);
					$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
				}
			}
			catch (Exception $e) {
				// Unknown service
				header('HTTP/1.0 404 Not Found',true,404);
				$_SESSION['errorMessages'][] = new Exception('open311/unknownService');
			}
		}
		// Provide the full service list
		else {
			$categoryList = new CategoryList();
			$categoryList->find();
			$this->template->blocks[] = new Block('open311/serviceList.inc',array('categoryList'=>$categoryList));
		}
	}

	/**
	 * @param REQUEST service_request_id
	 */
	public function requests()
	{
		if (!empty($_REQUEST['service_code'])) {
			try {
				$category = new Category($_REQUEST['service_code']);
			}
			catch (Exception $e) {
				header('HTTP/1.0 404 Not Found', true, 404);
				$_SESSION['errorMessages'][] = $e;
				return;
			}
		}

		// Display a single request
		if (!empty($_REQUEST['service_request_id'])) {
			try {
				$ticket = new Ticket($_REQUEST['service_request_id']);
				if ($ticket->allowsDisplay($this->person)) {
					$this->template->blocks[] = new Block('open311/requestInfo.inc',array('ticket'=>$ticket));
				}
				else {
					header('HTTP/1.0 403 Forbidden', true, 403);
					$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
				}
			}
			catch (Exception $e) {
				// Unknown ticket
				header('HTTP/1.0 404 Not Found', true, 404);
				$_SESSION['errorMessages'][] = $e;
				return;
			}
		}
		// Handle POST Service Request
		elseif (isset($_POST['service_code'])) {
			try {
				$ticket = new Ticket();
				$ticket->handleAdd(Open311Client::translatePostArray($_POST));

				// Media can only be attached after the ticket is saved
				// It uses the issue_id in the directory structure
				if (isset($_FILES['media'])) {
					$issue = $ticket->getIssue();
					try {
						$media = new Media();
						$media->setIssue($issue);
						$media->setFile($_FILES['media']);
					}
					catch (Exception $e) {
						// Just ignore any media errors for now
					}
				}
				$this->template->blocks[] = new Block('open311/requestInfo.inc',array('ticket'=>$ticket));
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				switch ($e->getMessage()) {
					case 'clients/unknownClient':
						header('HTTP/1.0 403 Forbidden',true,403);
						break;

					default:
						header('HTTP/1.0 400 Bad Request',true,400);
				}
			}
		}
		// Do a search for requests
		else {
			$search = array();
			if (isset($category) && $category->allowsDisplay($this->person)) {
				$search['category_id'] = $category->getId();
			}
			if (!empty($_REQUEST['start_date'])) {
				$search['start_date'] = $_REQUEST['start_date'];
			}
			if (!empty($_REQUEST['end_date'])) {
				$search['end_date'] = $_REQUEST['end_date'];
			}
			if (!empty($_REQUEST['status'])) {
				$search['status'] = $_REQUEST['status'];
			}

			$pageSize = 1000;
			if (!empty($_REQUEST['page_size'])) {
				$p = (int)$_REQUEST['page_size'];
				if ($p) { $pageSize = $p; }
			}
			// Pagination pages are one-based and will treat page=0
			// as exactly the same as page=1
			$page = 0;
			if (!empty($_REQUEST['page'])) {
				$p = (int)$_REQUEST['page'];
				if ($p) { $page = $p; }
			}
			$tickets = new TicketList();
			$tickets->find($search);
			$tickets->setPagination($pageSize, $page);
			$this->template->blocks[] = new Block('open311/requestList.inc',array('ticketList'=>$tickets));
		}
	}
}