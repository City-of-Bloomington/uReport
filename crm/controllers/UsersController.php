<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class UsersController extends Controller
{
	public function __construct(Template $template)
	{
		parent::__construct($template);
		$this->template->setFilename('two-column');
	}

	public function index()
	{
		$search = array('user_account'=>true);
		if (!empty($_GET['department_id'])) {
			$search['department_id'] = $_GET['department_id'];
		}
		$people = new PersonList($search);

		$this->template->blocks[] = new Block('users/userList.inc',array('userList'=>$people));
	}

	public function update()
	{
		if (isset($_REQUEST['person_id'])) {
			// Load the user for editing
			try {
				$user = new Person($_REQUEST['person_id']);
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				header('Location: '.BASE_URL.'/users');
				exit();
			}
		}
		else {
			$user = new Person();
		}

		// Handle POST data
		if (isset($_POST['username'])) {
			try {
				$user->handleUpdateUserAccount($_POST);
				$user->save();
				header('Location: '.BASE_URL.'/users');
				exit();
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				print_r($e);
				print_r($user);
				exit();
			}
		}

		// Display the form
		if ($user->getId()) {
			$this->template->blocks[] = new Block(
				'people/personInfo.inc',
				array('person'=>$user,'disableButtons'=>true)
			);
		}
		$this->template->blocks[] = new Block('users/updateUserForm.inc',array('person'=>$user));
	}

	/**
	 * Delets a Person's user account information
	 */
	public function delete()
	{
		$person = new Person($_GET['person_id']);
		$person->deleteUserAccount();
		try {
			$person->save();
		}
		catch (Exception $e) {
			$_SESSION['errorMessages'][] = $e;
		}

		header('Location: '.BASE_URL.'/users');
	}
}