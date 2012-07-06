<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class CategoryGroupsController extends Controller
{
	public function __construct(Template $template)
	{
		parent::__construct($template);
		$this->template->setFilename('backend');
	}

	public function index()
	{
		$list = new CategoryGroupList();
		$list->find();
		$this->template->blocks[] = new Block(
			'categoryGroups/list.inc',
			array('categoryGroupList'=>$list)
		);
	}

	public function update()
	{
		// Load the $client for editing
		if (isset($_REQUEST['categoryGroup_id']) && $_REQUEST['categoryGroup_id']) {
			try {
				$group = new CategoryGroup($_REQUEST['categoryGroup_id']);
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				header('Location: '.BASE_URL.'/categoryGroups');
				exit();
			}
		}
		else {
			$group = new CategoryGroup();
		}

		if (isset($_POST['name'])) {
			$group->handleUpdate($_POST);
			try {
				$group->save();
				header('Location: '.BASE_URL.'/categoryGroups');
				exit();
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->blocks[] = new Block(
			'categoryGroups/updateForm.inc',
			array('categoryGroup'=>$group)
		);
	}

	public function reorder()
	{
		if (isset($_POST['categoryGroups'])) {
			foreach ($_POST['categoryGroups'] as $id=>$order) {
				try {
					$group = new CategoryGroup($id);
					$group->setOrdering($order);
					$group->save();
				}
				catch (Exception $e) {
					$_SESSION['errorMessages'][] = $e;
				}
			}
			header('Location: '.BASE_URL.'/categoryGroups');
			exit();
		}

		$this->template->blocks[] = new Block('categoryGroups/reorderForm.inc');
	}

	public function delete()
	{
		try {
			$group = new CategoryGroup($_REQUEST['categoryGroup_id']);
			$group->delete();
		}
		catch (Exception $e) {
			$_SESSION['errorMessages'][] = $e;
		}
		header('Location: '.BASE_URL.'/categoryGroups');
		exit();
	}
}