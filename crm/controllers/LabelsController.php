<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class LabelsController extends Controller
{
	public function __construct(Template $template)
	{
		parent::__construct($template);
		$this->template->setFilename('two-column');
	}

	public function index()
	{
		$this->template->blocks[] = new Block('labels/list.inc');
	}

	public function update()
	{
		if (!empty($_REQUEST['label_id'])) {
			try {
				$label = new Label($_REQUEST['label_id']);
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				header('Location: '.BASE_URL.'/labels');
				exit();
			}
		}
		else {
			$label = new Label();
		}

		if (isset($_POST['name'])) {
			$label->handleUpdate($_POST);
			try {
				$label->save();
				header('Location: '.BASE_URL.'/labels');
				exit();
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->blocks[] = new Block('labels/updateForm.inc',array('label'=>$label));
	}
}