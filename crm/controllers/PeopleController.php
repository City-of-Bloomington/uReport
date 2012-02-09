<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class PeopleController extends Controller
{
	/**
	 * Find and choose people
	 *
	 * The user can come here from somewhere they need a person
	 * Choosing a person should send them back where they came from,
	 * with the chosen person appended to the url
	 *
	 * @param GET return_url
	 */
	public function index()
	{
		// Look for anything that the user searched for
		$search = array();
		$fields = array('firstname','lastname','email','organization','department');
		foreach ($fields as $field) {
			if (isset($_GET[$field]) && $_GET[$field]) {
				$value = trim($_GET[$field]);
				if ($value) {
					$search[$field] = $value;
				}
			}
		}

		// Display the search form and any results
		if ($this->template->outputFormat == 'html') {
			$searchForm = new Block('people/searchForm.inc');
			if (isset($_GET['return_url'])) {
				$searchForm->return_url = $_GET['return_url'];
			}
			$this->template->blocks[] = $searchForm;
		}

		if (count($search)) {
			if (isset($_GET['setOfPeople'])) {
				switch ($_GET['setOfPeople']) {
					case 'staff':
						$search['username'] = array('$exists'=>true);
						break;
					case 'public':
						$search['username'] = array('$exists'=>false);
						break;
				}
			}
			$personList = new PersonList();
			$personList->search($search);
			$searchResults = new Block('people/searchResults.inc',array('personList'=>$personList));
			if (isset($_GET['return_url'])) {
				$searchResults->return_url = $_GET['return_url'];
			}
			$this->template->blocks[] = $searchResults;
		}
	}

	/**
	 * Displays a single block
	 *
	 * The script is a mirror of ::index()
	 * It responds to the same requests, but also lets you specify
	 * a single block to to output.
	 *
	 * @param GET partial
	 */
	public function partial()
	{
		$block = new Block($_GET['partial']);

		if (isset($_GET['return_url'])) {
			$block->return_url = $_GET['return_url'];
		}
		if (isset($_GET['disableButtons'])) {
			$block->disableButtons = true;
		}

		// Look for anything that the user searched for
		$search = array();
		$fields = array('firstname','lastname','email','organization');
		foreach ($fields as $field) {
			if (isset($_GET[$field]) && $_GET[$field]) {
				$value = trim($_GET[$field]);
				if ($value) {
					$search[$field] = $value;
				}
			}
		}

		if (count($search)) {
			if (isset($_GET['setOfPeople'])) {
				switch ($_GET['setOfPeople']) {
					case 'staff':
						$search['username'] = array('$exists'=>true);
						break;
					case 'public':
						$search['username'] = array('$exists'=>false);
						break;
				}
			}
			$personList = new PersonList();
			$personList->search($search);
			$block->personList = $personList;
		}

		$this->template->setFilename('partial');
		$this->template->blocks[] = $block;
	}

	/**
	 * @param GET person_id
	 * @param GET disableLinks
	 */
	public function view()
	{
		$disableLinks = isset($_GET['disableLinks']) ? (bool)$_GET['disableLinks'] : false;
		$filename = isset($_GET['partial']) ? 'partial' : 'people';
		$this->template->setFilename($filename);

		if (!isset($_GET['person_id'])) {
			header('Location: '.BASE_URL.'/people');
			exit();
		}
		try {
			$person = new Person($_GET['person_id']);
		}
		catch (Exception $e) {
			$_SESSION['errorMessages'][] = $e;
			header('Location: '.BASE_URL.'/people');
			exit();
		}

		$this->template->title = $person->getFullname();
		$this->template->blocks['person-panel'][] = new Block('people/personInfo.inc',array('person'=>$person));
		if (!$disableLinks && userIsAllowed('tickets','add')) {
			$this->template->blocks['person-panel'][] = new Block(
				'tickets/addNewForm.inc',
				array('title'=>'Report New Case')
			);
		}
		$this->template->blocks['person-panel'][] = new Block('people/stats.inc',array('person'=>$person));

		$lists = array(
			'reportedBy'=>'Reported Cases',
			'assigned'=>'Assigned Cases',
			'referred'=>'Referred Cases',
			'enteredBy'=>'Entered Cases'
		);
		foreach ($lists as $listType=>$title) {
			$this->addTicketList($listType, $title, $person, $disableLinks);
		}
	}

	/**
	 * Adds a ticketList about the Person to the template
	 */
	private function addTicketList($listType, $title, Person $person, $disableLinks)
	{
		$tickets = $person->getTickets($listType);
		if (count($tickets)) {
			$this->template->blocks['person-panel'][] = new Block(
				'tickets/ticketList.inc',
				array(
					'ticketList'=>$tickets,
					'title'=>$title,
					'limit'=>10,
					'disableLinks'=>$disableLinks,
					'moreLink'=>BASE_URL."/tickets?{$listType}Person={$person->getId()}"
				)
			);
		}
	}

	public function update()
	{
		$errorURL = isset($_REQUEST['return_url']) ? $_REQUEST['return_url'] : BASE_URL.'/people';
		$return_url = isset($_REQUEST['return_url'])
			? new URL($_REQUEST['return_url'])
			: new URL(BASE_URL.'/people/view');

		if (isset($_REQUEST['person_id']) && $_REQUEST['person_id']) {
			try {
				$person = new Person($_REQUEST['person_id']);
				$return_url->person_id = "{$person->getId()}";
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				header("Location: $errorURL");
				exit();
			}
		}
		else {
			$person = new Person();
		}

		if (isset($_POST['firstname'])) {
			$fields = array(
				'firstname','middlename','lastname','email','phoneNumber','organization',
				'address','city','state','zip'
			);
			foreach ($fields as $field) {
				if (isset($_POST[$field])) {
					$set = 'set'.ucfirst($field);
					$person->$set($_POST[$field]);
				}
			}

			try {
				$person->save();
				$return_url->person_id = "{$person->getId()}";

				header("Location: $return_url");
				exit();
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->title = 'Update a person';
		$this->template->blocks[] = new Block(
			'people/updatePersonForm.inc',
			array('person'=>$person,'return_url'=>$return_url)
		);
	}

	public function delete()
	{
		try {
			$person = new Person($_GET['person_id']);
			$person->delete();
		}
		catch (Exception $e) {
			$_SESSION['errorMessages'][] = $e;
		}
		header('Location: '.BASE_URL.'/people');
		exit();
	}

	/**
	 * Moves all tickets from one person to another
	 */
	public function merge()
	{
		try {
			$personA = new Person($_GET['person_id_a']);
			$personB = new Person($_GET['person_id_b']);
		}
		catch (Exception $e) {
			$_SESSION['errorMessages'][] = $e;
			header('Location: '.BASE_URL);
			exit();
		}
		// When the user chooses a target, merge the other ticket into the target
		if (isset($_POST['targetPerson'])) {
			try {
				if ($_POST['targetPerson']=='a') {
					$personA->mergeFrom($personB);
					$targetPerson = $personA;
				}
				else {
					$personB->mergeFrom($personA);
					$targetPerson = $personB;
				}

				header('Location: '.$targetPerson->getURL());
				exit();
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}


		$this->template->setFilename('merging');
		$this->template->blocks[] = new Block(
			'people/mergeForm.inc',
			array('personA'=>$personA,'personB'=>$personB)
		);

		$this->template->blocks['merge-panel-one'][] = new Block(
			'people/personInfo.inc',
			array('person'=>$personA,'disableButtons'=>true)
		);
		$reportedTickets = $personA->getReportedTickets();
		if (count($reportedTickets)) {
			$this->template->blocks['merge-panel-one'][] = new Block(
				'tickets/searchResults.inc',
				array(
					'ticketList'=>$personA->getReportedTickets(),
					'title'=>'Tickets With Issues Reported By '.$personA->getFullname(),
					'disableButtons'=>true,
					'disableComments'=>true
				)
			);
		}

		$this->template->blocks['merge-panel-two'][] = new Block(
			'people/personInfo.inc',
			array('person'=>$personB,'disableButtons'=>true)
		);
		$reportedTickets = $personB->getReportedTickets();
		if (count($reportedTickets)) {
			$this->template->blocks['merge-panel-two'][] = new Block(
				'tickets/searchResults.inc',
				array(
					'ticketList'=>$personB->getReportedTickets(),
					'title'=>'Tickets With Issues Reported By '.$personB->getFullname(),
					'disableButtons'=>true,
					'disableComments'=>true
				)
			);
		}
	}

	/**
	 * Displays the list of distinct values for a given field and query
	 *
	 * Used primarily to support autocomplete on the person search form
	 *
	 * @param GET field
	 * @param GET query
	 */
	public function distinct()
	{
		$this->template->blocks[] = new Block(
			'people/distinctFieldValues.inc',
			array('results'=>Person::getDistinct($_GET['field'],$_GET['query'])));
	}
}