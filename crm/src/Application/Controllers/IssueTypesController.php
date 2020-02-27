<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Controllers;

use Application\Models\IssueType;
use Application\Models\IssueTypeTable;

use Blossom\Classes\Block;
use Blossom\Classes\Controller;
use Blossom\Classes\Template;

class IssueTypesController extends Controller
{
	public function index()
	{
        $table = new IssueTypeTable();
        $list  = $table->find();
        $this->template->title = $this->template->_(['issueType', 'issueTypes', count($list)]);
		$this->template->blocks[] = new Block('issueTypes/list.inc', ['issueTypes'=>$list]);
	}

	public function update()
	{
		if (!empty($_REQUEST['issueType_id'])) {
			try {
				$type = new IssueType($_REQUEST['issueType_id']);
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				header('Location: '.BASE_URL.'/issueTypes');
				exit();
			}
		}
		else {
			$type = new IssueType();
		}

		if (isset($_POST['name'])) {
			$type->handleUpdate($_POST);
			try {
				$type->save();
				header('Location: '.BASE_URL.'/issueTypes');
				exit();
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->title = $type->getId()
            ? $this->template->_('issueType_edit')
            : $this->template->_('issueType_add');
		$this->template->blocks[] = new Block('issueTypes/updateForm.inc', ['issueType'=>$type]);
	}
}
