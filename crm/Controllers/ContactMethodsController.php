<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;

use Application\Models\ContactMethod;
use Application\Models\ContactMethodTable;

use Blossom\Classes\Block;
use Blossom\Classes\Controller;
use Blossom\Classes\Template;

class ContactMethodsController extends Controller
{
	public function index()
	{
        $table = new ContactMethodTable();
        $list  = $table->find();
        $this->template->title = $this->template->_(['contactMethod', 'contactMethods', count($list)]);
		$this->template->blocks[] = new Block('contactMethods/list.inc', ['contactMethods'=>$list]);
	}

	public function update()
	{
		if (!empty($_REQUEST['contactMethod_id'])) {
			try {
				$method = new ContactMethod($_REQUEST['contactMethod_id']);
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				header('Location: '.BASE_URL.'/contactMethods');
				exit();
			}
		}
		else {
			$method = new ContactMethod();
		}

		if (isset($_POST['name'])) {
			$method->handleUpdate($_POST);
			try {
				$method->save();
				header('Location: '.BASE_URL.'/contactMethods');
				exit();
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->title = $method->getId()
            ? $this->template->_('contactMethod_edit')
            : $this->template->_('contactMethod_add');
		$this->template->blocks[] = new Block('contactMethods/updateForm.inc', ['contactMethod'=>$method]);
	}
}
