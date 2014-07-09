<?php
/**
 * @copyright 2012-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;

use Application\Models\ContactMethod;

use Blossom\Classes\Block;
use Blossom\Classes\Controller;
use Blossom\Classes\Template;

class ContactMethodsController extends Controller
{
	public function __construct(Template $template)
	{
		parent::__construct($template);
		$this->template->setFilename('backend');
	}

	public function index()
	{
		$this->template->blocks[] = new Block('contactMethods/list.inc');
	}

	public function update()
	{
		if (!empty($_REQUEST['contactMethod_id'])) {
			try {
				$method = new ContactMethod($_REQUEST['contactMethod_id']);
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				header('Location: '.BASE_URL.'/contactMethods');
				exit();
			}
		}
		else {
			$method = new ContactMethod();
		}

		if (isset($_POST['name'])) {
			$method->handleUpdate($_POST);
			try {
				$method->save();
				header('Location: '.BASE_URL.'/contactMethods');
				exit();
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->blocks[] = new Block(
			'contactMethods/updateForm.inc',
			array('contactMethod'=>$method)
		);
	}
}
