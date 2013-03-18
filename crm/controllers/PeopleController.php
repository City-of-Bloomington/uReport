<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class PeopleController extends Controller
{
	private function redirectToErrorUrl(Exception $e)
	{
		$_SESSION['errorMessages'][] = $e;
		header('Location: '.BASE_URL.'/people');
		exit();
	}

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
		$this->template->setFilename('people');
		if (isset($_REQUEST['callback'])) {
			$this->template->title = 'Choose Person';
		}

		// Look for anything that the user searched for
		$search = array();
		$fields = array('firstname','lastname','email','organization','department_id');
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
			$this->template->blocks['left'][] = $searchForm;
		}

		if (count($search)) {
			if (isset($_GET['setOfPeople'])) {
				switch ($_GET['setOfPeople']) {
					case 'staff':
						$search['user_account'] = true;
						break;
					case 'public':
						$search['user_account'] = false;
						break;
				}
			}
			$personList = new PersonList();
			$personList->search($search);
			$searchResults = new Block('people/searchResults.inc',array('personList'=>$personList));
			$this->template->blocks[] = $searchResults;
		}
	}

	/**
	 * @param GET person_id
	 * @param GET disableLinks
	 */
	public function view()
	{
		$this->template->setFilename('people');
		if (!isset($_GET['person_id'])) {
			$this->redirectToErrorUrl(new Exception('people/unknownPerson'));
		}
		try {
			$person = new Person($_GET['person_id']);
		}
		catch (Exception $e) {
			$this->redirectToErrorUrl($e);
		}
		$this->template->title = $person->getFullname();


		$disableButtons = isset($_REQUEST['disableButtons']) ? (bool)$_REQUEST['disableButtons'] : false;
		$block = new Block(
			'people/personInfo.inc',
			array('person'=>$person,'disableButtons'=>$disableButtons)
		);

		if ($this->template->outputFormat == 'html') {
			$this->template->blocks['left'][] = $block;
			$this->template->blocks['right'][] = new Block('people/stats.inc',array('person'=>$person));

			$lists = array(
				'reportedBy'=>'Reported Cases',
				'assigned'  =>'Assigned Cases',
				'referred'  =>'Referred Cases',
				'enteredBy' =>'Entered Cases'
			);
			$disableLinks = isset($_REQUEST['disableLinks']) ? (bool)$_REQUEST['disableLinks'] : false;
			foreach ($lists as $listType=>$title) {
				$this->addTicketList($listType, $title, $person, $disableLinks);
			}
		}
		else {
			$this->template->blocks[] = $block;
		}
	}

	/**
	 * Adds a ticketList about the Person to the template
	 *
	 * @param string $listType (enteredBy, assigned, reportedBy, referred)
	 * @param string $title
	 * @param Person $person
	 * @param bool $disableLinks
	 */
	private function addTicketList($listType, $title, Person $person, $disableLinks)
	{
		$field = $listType.'Person_id';

		$tickets = new TicketList();
		$tickets->find(array($field=>$person->getId()), 't.enteredDate desc', 10);

		if (count($tickets)) {
			$block = new Block(
				'tickets/ticketList.inc',
				array(
					'ticketList'  => $tickets,
					'title'       => $title,
					'disableLinks'=> $disableLinks
				)
			);
			if (count($tickets) >= 10) {
				$block->moreLink = BASE_URL."/tickets?{$listType}Person_id={$person->getId()}";
			}
			$this->template->blocks['right'][] = $block;
		}
	}

	public function update()
	{
		$errorURL = isset($_REQUEST['return_url']) ? $_REQUEST['return_url'] : BASE_URL.'/people';

		if (isset($_REQUEST['person_id']) && $_REQUEST['person_id']) {
			try {
				$person = new Person($_REQUEST['person_id']);
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
			try {
				$newRecord = $person->getId() ? false : true;

				$person->handleUpdate($_POST);
				$person->save();

				if ($newRecord) {
					if (!empty($_POST['email'])) {
						$email = new Email();
						$email->setPerson($person);
						$email->setEmail($_POST['email']);
						$email->save();
					}
					if (!empty($_POST['phone'])) {
						$phone = new Phone();
						$phone->setPerson($person);
						$phone->setNumber($_POST['phone']);
						$phone->save();
					}
				}

				if (isset($_REQUEST['return_url'])) {
					$return_url = new URL($_REQUEST['return_url']);
					$return_url->person_id = $person->getId();
				}
				elseif (isset($_REQUEST['callback'])) {
					$return_url = new URL(BASE_URL.'/callback');
					$return_url->callback = $_REQUEST['callback'];
					$return_url->data = "{$person->getId()}";
				}
				else {
					$return_url = $person->getURL();
				}
				header("Location: $return_url");
				exit();
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->title = 'Update a person';
		$this->template->blocks[] = new Block('people/updatePersonForm.inc',array('person'=>$person));
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

	public function updateEmail()
	{
		if (isset($_REQUEST['email_id'])) {
			try {
				$email = new Email($_REQUEST['email_id']);
			}
			catch (Exception $e) { $this->redirectToErrorUrl($e); }
		}
		else {
			$email = new Email();
		}

		if (!empty($_REQUEST['person_id'])) {
			try {
				$email->setPerson_id($_REQUEST['person_id']);
			}
			catch (Exception $e) { $this->redirectToErrorUrl($e); }
		}

		if (!$email->getPerson_id()) {
			$this->redirectToErrorUrl(new Exception('people/unknownPerson'));
		}


		if (isset($_POST['email'])) {
			try {
				$email->handleUpdate($_POST);
				$email->save();
				header('Location: '.$email->getPerson()->getUrl());
				exit();
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->blocks[] = new Block('people/updateEmailForm.inc', array('email'=>$email));
	}

	public function deleteEmail()
	{
		if (isset($_REQUEST['email_id'])) {
			try {
				$email = new Email($_REQUEST['email_id']);
				$person = $email->getPerson();
				$email->delete();
				header('Location: '.$person->getURL());
				exit();
			}
			catch (Exception $e) { $this->redirectToErrorUrl($e); }
		}
		else {
			$this->redirectToErrorUrl(new Exception('emails/unknownEmail'));
		}
	}

	public function updatePhone()
	{
		if (isset($_REQUEST['phone_id'])) {
			try {
				$phone = new Phone($_REQUEST['phone_id']);
			}
			catch (Exception $e) { $this->redirectToErrorUrl($e); }
		}
		else {
			$phone = new Phone();
		}

		if (!empty($_REQUEST['person_id'])) {
			try {
				$phone->setPerson_id($_REQUEST['person_id']);
			}
			catch (Exception $e) { $this->redirectToErrorUrl($e); }
		}

		if (!$phone->getPerson_id()) {
			$this->redirectToErrorUrl(new Exception('people/unknownPerson'));
		}


		if (isset($_POST['number'])) {
			try {
				$phone->handleUpdate($_POST);
				$phone->save();
				header('Location: '.$phone->getPerson()->getUrl());
				exit();
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->blocks[] = new Block('people/updatePhoneForm.inc', array('phone'=>$phone));
	}

	public function deletePhone()
	{
		if (isset($_REQUEST['phone_id'])) {
			try {
				$phone = new Phone($_REQUEST['phone_id']);
				$person = $phone->getPerson();
				$phone->delete();
				header('Location: '.$person->getURL());
				exit();
			}
			catch (Exception $e) { $this->redirectToErrorUrl($e); }
		}
		else {
			$this->redirectToErrorUrl(new Exception('phones/unknownPhone'));
		}
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

		$this->template->blocks['left'][] = new Block(
			'people/personInfo.inc',
			array('person'=>$personA,'disableButtons'=>true)
		);
		$reportedTickets = $personA->getReportedTickets();
		if (count($reportedTickets)) {
			$this->template->blocks['left'][] = new Block(
				'tickets/searchResults.inc',
				array(
					'ticketList'=>$personA->getReportedTickets(),
					'title'=>'Tickets With Issues Reported By '.$personA->getFullname(),
					'disableButtons'=>true,
					'disableComments'=>true
				)
			);
		}

		$this->template->blocks['right'][] = new Block(
			'people/personInfo.inc',
			array('person'=>$personB,'disableButtons'=>true)
		);
		$reportedTickets = $personB->getReportedTickets();
		if (count($reportedTickets)) {
			$this->template->blocks['right'][] = new Block(
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