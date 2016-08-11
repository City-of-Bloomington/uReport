<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;

use Application\Models\Category;
use Application\Models\Department;
use Application\Models\DepartmentTable;

use Blossom\Classes\Block;
use Blossom\Classes\Controller;
use Blossom\Classes\Template;

class DepartmentsController extends Controller
{
	public function index()
	{
		$table = new DepartmentTable();
		$departmentList = $table->find();

		$this->template->blocks[] = new Block('departments/departmentList.inc', ['departmentList'=>$departmentList]);
	}

	public function view()
	{
        if (!empty($_GET['department_id'])) {
            $department = new Department($_GET['department_id']);
        }
        elseif (!empty($_GET['category_id'])) {
            $category = new Category($_GET['category_id']);
            $department = $category->getDepartment();
        }

        if (isset($department)) {
            $this->template->blocks[] = new Block('departments/departmentInfo.inc', ['department'=>$department]);
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
	}

	public function update()
	{
		// Load the department for editing
		if (isset($_REQUEST['department_id']) && $_REQUEST['department_id']) {
			try {
				$department = new Department($_REQUEST['department_id']);
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				header('Location: '.BASE_URL.'/departments');
				exit();
			}
		}
		else {
			$department = new Department();
		}

		if (isset($_POST['name'])) {
			try {
                // The department will call save() as needed
				$department->handleUpdate($_POST);
				$department->save();

				header('Location: '.BASE_URL.'/departments/view?department_id='.$department->getId());
				exit();
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->blocks[] = new Block('departments/updateDepartmentForm.inc', [
            'department' => $department,
            'action'     => BASE_URI.'/departments/update',
            'return_url' => BASE_URI.'/departments'
        ]);
	}

	public function delete()
	{
		try {
			$department = new Department($_GET['department_id']);
			$department->delete();
		}
		catch (\Exception $e) {
			$_SESSION['errorMessages'][] = $e;
		}

		header('Location: '.BASE_URL.'/departments');
		exit();
	}
}
