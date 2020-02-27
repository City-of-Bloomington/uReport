<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Controllers;

use Application\Models\Person;
use Application\Models\PersonTable;
use Application\Models\Email;
use Application\Models\Phone;
use Application\Models\TicketTable;

use Blossom\Classes\Block;
use Blossom\Classes\Controller;
use Blossom\Classes\Template;
use Blossom\Classes\Url;

class PeopleController extends Controller
{
	private function redirectToErrorUrl(\Exception $e)
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
		if (isset($_REQUEST['callback'])) {
			$this->template->title = $this->template->_('use_person');
		}

		// Look for anything that the user searched for
		$search = [];
		$fields = ['firstname', 'lastname', 'email', 'organization', 'department_id'];
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
			$this->template->blocks[] = $searchForm;
		}

		if (count($search)) {
			if (isset(  $_GET['setOfPeople'])) {
				switch ($_GET['setOfPeople']) {
					case 'staff':
						$search['user_account'] = true;
						break;
					case 'public':
						$search['user_account'] = false;
						break;
				}
			}
			$table = new PersonTable();
			$personList = $table->search($search);
			$searchResults = new Block('people/searchResults.inc', ['personList'=>$personList]);
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
			$this->redirectToErrorUrl(new \Exception('people/unknownPerson'));
		}

		try { $person = new Person($_GET['person_id']); }
		catch (\Exception $e) { $this->redirectToErrorUrl($e); }
		$this->template->title = $person->getFullname();

		$disableButtons = isset($_REQUEST['disableButtons']) ? (bool)$_REQUEST['disableButtons'] : false;
		$block = new Block(
			'people/personInfo.inc',
			['person'=>$person,'disableButtons'=>$disableButtons]
		);

		if ($this->template->outputFormat == 'html') {
			$this->template->blocks['panel-one'][] = $block;
			$this->template->blocks['panel-two'][] = new Block('people/stats.inc', ['person'=>$person]);

			$lists = [
				'reportedBy'=> $this->template->_('tickets_reported'),
				'assigned'  => $this->template->_('tickets_assigned'),
				'enteredBy' => $this->template->_('tickets_entered')
			];
			$disableLinks = isset($_REQUEST['disableLinks']) ? (bool)$_REQUEST['disableLinks'] : false;
			$count = 0;
			foreach ($lists as $listType=>$title) {
				$count += $this->addTicketList('panel-two', $listType, $title, $person, $disableLinks);
			}
		}
		else {
			$this->template->blocks[] = $block;
		}
	}

	/**
	 * Adds a ticketList about the Person to the template
	 *
	 * @param string $panel
	 * @param string $listType (enteredBy, assigned, reportedBy)
	 * @param string $title
	 * @param Person $person
	 * @param bool $disableLinks
	 *
	 * @return int The number of tickets displayed in the list
	 */
	private function addTicketList($panel, $listType, $title, Person $person, $disableLinks=false, $disableButtons=false)
	{
		$field = $listType.'Person_id';

		$table = new TicketTable();
		$tickets = $table->find([$field=>$person->getId()], 'tickets.enteredDate desc', true);
		$tickets->setCurrentPageNumber(1);
		$tickets->setItemCountPerPage(10);

		$numPages = count($tickets);
		if ($numPages) {
			$block = new Block('tickets/ticketList.inc', [
                'ticketList'    => $tickets,
                'title'         => $title,
                'disableLinks'  => $disableLinks,
                'disableButtons'=> $disableButtons,
                'fields'        => ['status', 'enteredDate', 'category', 'location']
            ]);
			if ($numPages > 1) {
				$block->moreLink = BASE_URL."/tickets?{$listType}Person_id={$person->getId()}";
			}
			$this->template->blocks[$panel][] = $block;
		}
		return $numPages;
	}

	public function update()
	{
		$errorURL = isset($_REQUEST['return_url']) ? $_REQUEST['return_url'] : BASE_URL.'/people';

		if (isset($_REQUEST['person_id']) && $_REQUEST['person_id']) {
			try {
				$person = new Person($_REQUEST['person_id']);
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				header("Location: $errorURL");
				exit();
			}
		}
		else {
			$person = new Person();
			if (isset($_GET)) { $person->handleUpdate($_GET); }
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
					$return_url = new Url($_REQUEST['return_url']);
					$return_url->person_id = $person->getId();
				}
				elseif (isset($_REQUEST['callback'])) {
					$return_url = new Url(BASE_URL.'/callback');
					$return_url->callback = $_REQUEST['callback'];
					$return_url->data = "{$person->getId()}";
				}
				else {
					$return_url = $person->getURL();
				}
				header("Location: $return_url");
				exit();
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->title = $person->getId()
            ? $this->template->_('person_edit')
            : $this->template->_('person_add');
		$this->template->blocks[] = new Block('people/updatePersonForm.inc', ['person'=>$person]);
	}

	public function delete()
	{
		try {
			$person = new Person($_GET['person_id']);
			$person->delete();
		}
		catch (\Exception $e) {
			$_SESSION['errorMessages'][] = $e;
		}
		header('Location: '.BASE_URL.'/people');
		exit();
	}

	/**
	 * Helper function for handling foreign key object deletions
	 *
	 * Email, Phone, and Address are all handled exactly the same way.
	 *
	 * @param string $item
	 */
	private function deleteLinkedItem($item)
	{
		$class = 'Application\\Models\\'.ucfirst($item);

		if (isset($_REQUEST[$item.'_id'])) {
			try {
				$o = new $class($_REQUEST[$item.'_id']);
				$person = $o->getPerson();
				$o->delete();
				header('Location: '.$person->getURL());
				exit();
			}
			catch (\Exception $e) { $this->redirectToErrorUrl($e); }
		}
		else {
			$this->redirectToErrorUrl(new \Exception("people/unknown$class"));
		}
	}
	public function deleteEmail()   { $this->deleteLinkedItem('email');   }
	public function deletePhone()   { $this->deleteLinkedItem('phone');   }
	public function deleteAddress() { $this->deleteLinkedItem('address'); }

	/**
	 * Helper function for handling foreign key object updates
	 *
	 * Email, Phone, and Address are all handled exactly the same way.
	 *
	 * @param string $item
	 * @param string $requiredField The field to look for in the POST which
	 *								determines whether this item has been posted
	 */
	private function updateLinkedItem($item, $requiredField)
	{
		$this->template->setFilename('people');
		$basename = ucfirst($item);
		$class = "Application\\Models\\$basename";

		if (isset($_REQUEST[$item.'_id'])) {
			try {
				$object = new $class($_REQUEST[$item.'_id']);
			}
			catch (\Exception $e) { $this->redirectToErrorUrl($e); }
		}
		else {
			$object = new $class();
		}

		if (!empty($_REQUEST['person_id'])) {
			try {
				$object->setPerson_id($_REQUEST['person_id']);
			}
			catch (\Exception $e) { $this->redirectToErrorUrl($e); }
		}

		if (!$object->getPerson_id()) {
			$this->redirectToErrorUrl(new \Exception('people/unknownPerson'));
		}


		if (isset($_POST[$requiredField])) {
			try {
				$object->handleUpdate($_POST);
				$object->save();
				header('Location: '.$object->getPerson()->getUrl());
				exit();
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$a = $object->getId() ? 'edit' : 'add';
		$this->template->title = $this->template->_("{$item}_{$a}");
		$this->template->blocks['panel-one'][] = new Block('people/personInfo.inc', ['person'=>$object->getPerson(), 'disableButtons'=>true]);
		$this->template->blocks['panel-two'][] = new Block("people/update{$basename}Form.inc", [$item=>$object]);
	}
	public function updateEmail()   { $this->updateLinkedItem('email',   'email');   }
	public function updatePhone()   { $this->updateLinkedItem('phone',   'number');  }
	public function updateAddress() { $this->updateLinkedItem('address', 'address'); }


	/**
	 * Moves all linked information from one person to another
	 */
	public function merge()
	{
		try {
			$personA = new Person($_REQUEST['person_id_a']);
			$personB = new Person($_REQUEST['person_id_b']);
		}
		catch (\Exception $e) {
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
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}


		$this->template->setFilename('merging');
		$this->template->title = $this->template->_('merge_people');
		$this->template->blocks[]          = new Block('people/mergeForm.inc',  ['personA'=>$personA, 'personB'=>$personB]);
		$this->template->blocks['left' ][] = new Block('people/personInfo.inc', ['person' =>$personA, 'disableButtons'=>true]);
		$this->template->blocks['right'][] = new Block('people/personInfo.inc', ['person' =>$personB, 'disableButtons'=>true]);

        $lists = [
            'reportedBy'=> $this->template->_('tickets_reported'),
            'assigned'  => $this->template->_('tickets_assigned'),
            'enteredBy' => $this->template->_('tickets_entered')
        ];
		foreach ($lists as $listType=>$title) { $this->addTicketList('left',  $listType, $title, $personA, true, true); }
		foreach ($lists as $listType=>$title) { $this->addTicketList('right', $listType, $title, $personB, true, true); }
	}

	/**
	 * Displays the list of distinct values for a given field and term
	 *
	 * Used primarily to support autocomplete on the person search form
	 *
	 * @param GET field
	 * @param GET term
	 */
	public function distinct()
	{
		$this->template->blocks[] = new Block('people/distinctFieldValues.inc', [
            'results' => Person::getDistinct($_GET['field'], $_GET['term'])
        ]);
	}
}
