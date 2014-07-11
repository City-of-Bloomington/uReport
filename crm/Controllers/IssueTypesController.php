<?php
/**
 * @copyright 2012-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;

use Application\Models\IssueType;

use Blossom\Classes\Block;
use Blossom\Classes\Controller;
use Blossom\Classes\Template;

class IssueTypesController extends Controller
{
	public function __construct(Template $template)
	{
		parent::__construct($template);
		$this->template->setFilename('backend');
	}

	public function index()
	{
		$this->template->blocks[] = new Block('issueTypes/list.inc');
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

		$this->template->blocks[] = new Block('issueTypes/updateForm.inc',array('issueType'=>$type));
	}
}
