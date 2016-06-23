<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;

use Application\Models\Action;

use Blossom\Classes\Block;
use Blossom\Classes\Controller;
use Blossom\Classes\Template;

class ActionsController extends Controller
{
	public function __construct(Template $template)
	{
		parent::__construct($template);
		$this->template->setFilename('backend');
	}

	public function index()
	{
		$this->template->blocks[] = new Block('actions/actionList.inc');
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

		$this->template->blocks[] = new Block('actions/updateActionForm.inc', ['action'=>$action]);
	}
}
