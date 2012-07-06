<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class ResolutionsController extends Controller
{
	public function __construct(Template $template)
	{
		parent::__construct($template);
		$this->template->setFilename('backend');
	}

	public function index()
	{
		$resolutionList = new ResolutionList();
		$resolutionList->find();

		$this->template->blocks[] = new Block(
			'resolutions/resolutionList.inc',
			array('resolutionList'=>$resolutionList)
		);
	}

	public function update()
	{
		// Load the $resolution for editing
		if (isset($_REQUEST['resolution_id']) && $_REQUEST['resolution_id']) {
			try {
				$resolution = new Resolution($_REQUEST['resolution_id']);
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				header('Location: '.BASE_URL.'/resolutions');
				exit();
			}
		}
		else {
			$resolution = new Resolution();
		}


		if (isset($_POST['name'])) {
			$resolution->handleUpdate($_POST);
			try {
				$resolution->save();
				header('Location: '.BASE_URL.'/resolutions');
				exit();
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->blocks[] = new Block(
			'resolutions/updateResolutionForm.inc',
			array('resolution'=>$resolution)
		);
	}
}