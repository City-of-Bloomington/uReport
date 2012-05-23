<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class ActionsController extends Controller
{
	public function __construct(Template $template)
	{
		parent::__construct($template);
		$this->template->setFilename('two-column');
	}

	public function index()
	{
		$this->template->blocks[] = new Block('actions/actionList.inc');
	}

	public function update()
	{
		// Load the $action for editing
		if (isset($_REQUEST['action_id']) && $_REQUEST['action_id']) {
			try {
				$action = new Action($_REQUEST['action_id']);
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				header('Location: '.BASE_URL.'/actions');
				exit();
			}
		}
		else {
			$action = new Action();
		}


		if (isset($_POST['name'])) {
			$action->handleUpdate($_POST);
			try {

				$action->save();
				header('Location: '.BASE_URL.'/actions');
				exit();
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->blocks[] = new Block('actions/updateActionForm.inc',array('action'=>$action));
	}
}