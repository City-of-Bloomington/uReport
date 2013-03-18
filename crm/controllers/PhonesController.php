<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class PhonesController extends Controller
{
	public function index()
	{
	}

	private function redirectToErrorUrl(Exception $e)
	{
		$_SESSION['errorMessages'][] = $e;
		header('Location: '.BASE_URL.'/people');
		exit();
	}

	public function update()
	{
		if (isset($_REQUEST['phone_id'])) {
			try {
				$phone = new Phone($_REQUEST['phone_id']);
			}
			catch (Exception $e) { $this->redirectToErrorUrl($e); }
		}
		else {
			$phone = new Phone();
		}

		if (!empty($_REQUEST['person_id'])) {
			try {
				$phone->setPerson_id($_REQUEST['person_id']);
			}
			catch (Exception $e) { $this->redirectToErrorUrl($e); }
		}

		if (!$phone->getPerson_id()) {
			$this->redirectToErrorUrl(new Exception('people/unknownPerson'));
		}


		if (isset($_POST['number'])) {
			try {
				$phone->handleUpdate($_POST);
				$phone->save();
				header('Location: '.$phone->getPerson()->getUrl());
				exit();
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->blocks[] = new Block('phones/updateForm.inc', array('phone'=>$phone));
	}

	public function delete()
	{
		if (isset($_REQUEST['phone_id'])) {
			try {
				$phone = new Phone($_REQUEST['phone_id']);
				$person = $phone->getPerson();
				$phone->delete();
				header('Location: '.$person->getURL());
				exit();
			}
			catch (Exception $e) { $this->redirectToErrorUrl($e); }
		}
		else {
			$this->redirectToErrorUrl(new Exception('phones/unknownPhone'));
		}
	}
}