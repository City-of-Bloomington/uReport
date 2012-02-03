<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class MediaController extends Controller
{
	/**
	 * @param string $id
	 */
	private function loadTicket($id)
	{
		try {
			$ticket = new Ticket($id);
			return $ticket;
		}
		catch (Exception $e) {
			$_SESSION['errorMessages'][] = $e;
			header('Location: '.BASE_URL);
			exit();
		}
	}

	public function index()
	{
	}

	/**
	 * @param GET ticket_id
	 * @param GET issueIndex
	 * @param GET mediaIndex
	 */
	public function delete()
	{
		$ticket = $this->loadTicket($_GET['ticket_id']);
		$ticket->deleteMedia($_GET['issueIndex'],$_GET['mediaIndex']);

		header('Location: '.$ticket->getURL());
		exit();
	}

	/**
	 * @param POST ticket_id
	 * @param POST index
	 * @param POST attachment
	 * @param POST return_url
	 */
	public function upload()
	{
		$ticket = $this->loadTicket($_POST['ticket_id']);
		try {
			$ticket->attachMedia($_FILES['attachment'],$_POST['index']);
			$ticket->save();
		}
		catch (Exception $e) {
			$_SESSION['errorMessages'][] = $e;
		}

		$return_url = isset($_POST['return_url']) ? $_POST['return_url'] : $ticket->getURL();
		header('Location: '.$return_url);
		exit();
	}
}