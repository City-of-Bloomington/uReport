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
		$this->template->setFilename('backend');
	}

	public function index()
	{
	}

	public function update()
	{
		$_SESSION['USER'] = new Person($_SESSION['USER']->getId());

		if (isset($_POST['firstname'])) {
			$_SESSION['USER']->handleUpdate($_POST);
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
		$return_url = BASE_URL.'/account';

		// Load the User's department
		$_SESSION['USER'] = new Person($_SESSION['USER']->getId());
		$department = $_SESSION['USER']->getDepartment();

		if (!$department) {
			$_SESSION['errorMessages'][] = new Exception('departments/unknownDepartment');
			header('Location: '.$return_url);
			exit();
		}

		// Handle any data they post
		if (isset($_POST['name'])) {
			try {
				$department->handleUpdate($_POST);
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
			array(
				'department'=>$department,
				'action'=>BASE_URI.'/account/updateMyDepartment',
				'return_url'=>BASE_URI.'/account'
			)
		);
	}
}