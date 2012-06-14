<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class DepartmentsController extends Controller
{
	public function __construct(Template $template)
	{
		parent::__construct($template);
		if ($this->template->outputFormat == 'html') {
			$this->template->setFilename('backend');
		}
	}

	public function index()
	{
		$departmentList = new DepartmentList();
		$departmentList->find();

		$this->template->blocks[] = new Block(
			'departments/departmentList.inc',
			array('departmentList'=>$departmentList)
		);
	}

	public function view()
	{
		$department = new Department($_GET['department_id']);

		$this->template->blocks[] = new Block(
			'departments/departmentInfo.inc',
			array('department'=>$department)
		);
	}

	public function update()
	{
		// Load the department for editing
		if (isset($_REQUEST['department_id']) && $_REQUEST['department_id']) {
			try {
				$department = new Department($_REQUEST['department_id']);
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				header('Location: '.BASE_URL.'/departments');
				exit();
			}
		}
		else {
			$department = new Department();
		}

		$return_url = isset($_REQUEST['return_url']) ? $_REQUEST['return_url'] : BASE_URL.'/departments';

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

		$this->template->blocks[] = new Block(
			'departments/updateDepartmentForm.inc',
			array('department'=>$department,'return_url'=>$return_url)
		);
	}

	public function delete()
	{
		try {
			$department = new Department($_GET['department_id']);
			$department->delete();
		}
		catch (Exception $e) {
			$_SESSION['errorMessages'][] = $e;
		}

		header('Location: '.BASE_URL.'/departments');
		exit();
	}
}