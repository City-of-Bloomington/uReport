<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Controllers;

use Application\Models\Substatus;
use Application\Models\SubstatusTable;

use Blossom\Classes\Block;
use Blossom\Classes\Controller;
use Blossom\Classes\Template;

class SubstatusController extends Controller
{
	public function index()
	{
		$table = new SubstatusTable();
		$list = !empty($_REQUEST['status'])
			? $table->find(['status'=>$_REQUEST['status']])
			: $table->find();

        $this->template->title = $this->template->_(['substatus', 'substatuses', count($list)]);
		$this->template->blocks[] = new Block('substatus/list.inc', ['substatusList'=>$list]);
	}

	public function update()
	{
		// Load the $substatus for editing
		if (isset($_REQUEST['substatus_id']) && $_REQUEST['substatus_id']) {
			try {
				$substatus = new Substatus($_REQUEST['substatus_id']);
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				header('Location: '.BASE_URL.'/substatus');
				exit();
			}
		}
		else {
			$substatus = new Substatus();
		}


		if (isset($_POST['name'])) {
			$substatus->handleUpdate($_POST);
			try {
				$substatus->save();
				header('Location: '.BASE_URL.'/substatus');
				exit();
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->title = $substatus->getId()
            ? $this->template->_('edit')
            : $this->template->_('add');
		$this->template->blocks[] = new Block('substatus/updateForm.inc', ['substatus'=>$substatus]);
	}
}
