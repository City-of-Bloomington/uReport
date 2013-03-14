<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class SubstatusController extends Controller
{
	public function __construct(Template $template)
	{
		parent::__construct($template);
		$this->template->setFilename('backend');
	}

	public function index()
	{
		$list = new SubstatusList();
		!empty($_REQUEST['status'])
			? $list->find(array('status'=>$_REQUEST['status']))
			: $list->find();

		$this->template->blocks[] = new Block('substatus/list.inc',array('substatusList'=>$list));
	}

	public function update()
	{
		// Load the $substatus for editing
		if (isset($_REQUEST['substatus_id']) && $_REQUEST['substatus_id']) {
			try {
				$substatus = new Substatus($_REQUEST['substatus_id']);
			}
			catch (Exception $e) {
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
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->blocks[] = new Block(
			'substatus/updateForm.inc',
			array('substatus'=>$substatus)
		);
	}
}