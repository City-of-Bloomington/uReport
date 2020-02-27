<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Controllers;

use Application\Models\Action;
use Application\Models\ActionTable;

use Blossom\Classes\Block;
use Blossom\Classes\Controller;
use Blossom\Classes\Template;

class ActionsController extends Controller
{
	public function index()
	{
        $table = new ActionTable();
        $list  = $table->find();
		$this->template->blocks[] = new Block('actions/actionList.inc', ['actions'=>$list]);
		$this->template->title = $this->template->_(['action', 'actions', count($list)]);
	}

	public function view()
	{
        if (!empty($_GET['action_id'])) {
            try { $action = new Action($_GET['action_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (isset($action)) {
            $this->template->blocks[] = new Block('actions/info.inc', ['action'=>$action]);
            $this->template->title = $action->getName();
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
	}

	public function update()
	{
		// Load the $action for editing
		if (!empty($_REQUEST['action_id'])) {
			try {
				$action = new Action($_REQUEST['action_id']);
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				header('Location: '.BASE_URL.'/actions');
				exit();
			}
		}
		else {
			$action = new Action();
		}


		if (isset($_POST['description'])) {
			$action->handleUpdate($_POST);
			try {
				$action->save();
				header('Location: '.BASE_URL.'/actions');
				exit();
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->title = $action->getId()
            ? $this->template->_('action_edit')
            : $this->template->_('action_add' );
		$this->template->blocks[] = new Block('actions/updateActionForm.inc', ['action'=>$action]);
	}
}
