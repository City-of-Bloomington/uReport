<?php
/**
 * @copyright 2012-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;

use Application\Models\Category;
use Application\Models\CategoryTable;

use Blossom\Classes\Block;
use Blossom\Classes\Controller;
use Blossom\Classes\Template;
use Blossom\Classes\Url;

class CategoriesController extends Controller
{
	public function index()
	{
		$t = new CategoryTable;
		$categoryList = $t->find();

		$this->template->setFilename('backend');
		$this->template->blocks[] = new Block(
			'categories/categoryList.inc',
			array('categoryList'=>$categoryList)
		);
	}

	public function view()
	{
		if ($this->template->outputFormat == 'html') {
			$this->template->setFilename('backend');
		}

		if (!empty($_REQUEST['category_id'])) {
			try {
				$category = new Category($_REQUEST['category_id']);
				$this->template->blocks[] = new Block('categories/info.inc', array('category'=>$category));
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}
	}

	public function update()
	{
		// Load the $category for editing
		if (isset($_REQUEST['category_id']) && $_REQUEST['category_id']) {
			try {
				$category = new Category($_REQUEST['category_id']);
			}
			catch (\Exception $e) {
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
				$category->handleUpdate($_POST);
				$category->save();
				header('Location: '.BASE_URL.'/categories');
				exit();
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->setFilename('backend');
		$this->template->blocks[] = new Block('categories/updateCategoryForm.inc',array('category'=>$category));
	}

	/**
	 * Displays the list of all the categories
	 *
	 * Each category will be linked back to $return_url
	 */
	public function choose()
	{
		$return_url = !empty($_GET['return_url'])
			? new Url($_GET['return_url'])
			: new Url(BASE_URL.'/categories/view');

		$t = new CategoryTable();
		$categoryList = $t->find(null, 'categories.name');

		$this->template->blocks[] = new Block(
			'categories/categoryChoices.inc',
			array('categoryList'=>$categoryList,'return_url'=>$return_url)
		);
	}

	/**
	 * A form for updating the SLA times for all categories at once
	 */
	public function sla()
	{
		$this->template->setFilename('backend');

		if (isset($_POST['categories'])) {
			try {
				foreach ($_POST['categories'] as $id=>$post) {
					$category = new Category($id);
					$category->setSlaDays($post['slaDays']);
					$category->save();
				}
				header('Location: '.BASE_URL.'/categories');
				exit();
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$t = new CategoryTable();
		$list = $t->find();

		$this->template->blocks[] = new Block('categories/slaForm.inc', ['categoryList'=>$list]);
	}
}
