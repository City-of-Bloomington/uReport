<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class CategoriesController extends Controller
{
	public function index()
	{
		$categoryList = new CategoryList();
		$categoryList->find();

		$this->template->setFilename('two-column');
		$this->template->blocks[] = new Block('categories/categoryList.inc',array('categoryList'=>$categoryList));
	}

	public function update()
	{
		// Load the $category for editing
		if (isset($_REQUEST['category_id']) && $_REQUEST['category_id']) {
			try {
				$category = new Category($_REQUEST['category_id']);
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				header('Location: '.BASE_URL.'/categories');
				exit();
			}
		}
		else {
			$category = new Category();
		}


		if (isset($_POST['name'])) {
			try {
				$category->set($_POST);
				$category->save();
				header('Location: '.BASE_URL.'/categories');
				exit();
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->setFilename('two-column');
		$this->template->blocks[] = new Block('categories/updateCategoryForm.inc',array('category'=>$category));
	}

	/**
	 * Displays the list of all the categories
	 *
	 * Each category will be linked back to $return_url
	 */
	public function choose()
	{
		$return_url = new URL($_GET['return_url']);

		$categoryList = new CategoryList();
		$categoryList->find();

		$this->template->blocks[] = new Block(
			'categories/categoryChoices.inc',
			array('categoryList'=>$categoryList,'return_url'=>$return_url)
		);
	}

	/**
	 * Displays the list of distinct category groups
	 *
	 * This function is primarily intended for web service calls.
	 * Although viewing it as HTML won't hurt anything
	 */
	public function groups()
	{
		$this->template->blocks[] = new Block(
			'categories/groups.inc',
			array('groups'=>Category::getDistinct('group'))
		);
	}
}