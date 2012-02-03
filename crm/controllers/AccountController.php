<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class AccountController extends Controller
{
	public function __construct(Template $template)
	{
		parent::__construct($template);
		$this->template->setFilename('two-column');
	}

	public function index()
	{
	}

	public function update()
	{
		if (isset($_POST['firstname'])) {
			$fields = array(
				'firstname','middlename','lastname','email','phoneNumber','organization',
				'address','city','state','zip'
			);
			foreach ($fields as $field) {
				if (isset($_POST[$field])) {
					$set = 'set'.ucfirst($field);
					$_SESSION['USER']->$set($_POST[$field]);
				}
			}

			try {
				$_SESSION['USER']->save();
				header('Location: '.BASE_URL.'/account');
				exit();
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->blocks[] = new Block(
			'people/updatePersonForm.inc',
			array(
				'person'=>$_SESSION['USER'],
				'title'=>'Update my info',
				'return_url'=>BASE_URI.'/account'
			)
		);
	}

	public function updateMyDepartment()
	{
		$return_url = BASE_URI.'/account';

		// Load the User's department
		$department = $_SESSION['USER']->getDepartment();
		if ($department) {
			try {
				$department = new Department($department['_id']);
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				header('Location: '.$return_url);
				exit();
			}
		}
		else {
			$_SESSION['errorMessages'][] = new Exception('departments/unknownDepartment');
			header('Location: '.$return_url);
			exit();
		}

		// Handle any data they post
		if (isset($_POST['name'])) {
			$department->setName($_POST['name']);
			$department->setCustomStatuses($_POST['customStatuses']);
			try {
				if ($_POST['defaultPerson']) {
					$department->setDefaultPerson($_POST['defaultPerson']);
				}
				$categories = isset($_POST['categories']) ? array_keys($_POST['categories']) : array();
				$actions = isset($_POST['actions']) ? array_keys($_POST['actions']) : array();

				$department->setCategories($categories);
				$department->setActions($actions);

				$department->save();
				header('Location: '.$return_url);
				exit();
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		// Display the form
		$this->template->blocks[] = new Block(
			'departments/updateDepartmentForm.inc',
			array('department'=>$department,'return_url'=>$return_url)
		);
	}
}