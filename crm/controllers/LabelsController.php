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
		$this->template->blocks[] = new Block('lookups/labelList.inc');
	}

	public function update()
	{
		if (isset($_POST['labels'])) {
			try {
				Lookups::save('labels',$_POST['labels']);
				header('Location: '.BASE_URL.'/labels');
				exit();
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->blocks[] = new Block('lookups/updateLabelsForm.inc');
	}
}