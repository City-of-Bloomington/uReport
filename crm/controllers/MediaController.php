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
	 */
	public function upload()
	{
		$ticket = $this->loadTicket($_REQUEST['ticket_id']);
		$issues = $ticket->getIssues();
		if (!isset($issues[$_REQUEST['index']])) {
			$_SESSION['errorMessages'][] = new Exception('tickets/unknownIssue');
			header('Location: '.$ticket->getURL());
			exit();
		}
		$issue = $issues[$_REQUEST['index']];

		if (isset($_FILES['attachment'])) {
			try {
				$ticket->attachMedia($_FILES['attachment'],$_POST['index']);
				$ticket->save();
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}

			header('Location: '.$ticket->getURL());
			exit();
		}

		$this->template->setFilename('tickets');
		$this->template->blocks['ticket-panel'][] = new Block(
			'tickets/ticketInfo.inc',
			array('ticket'=>$ticket,'disableButtons'=>1)
		);
		$this->template->blocks['ticket-panel'][] = new Block(
			'media/uploadForm.inc',
			array('ticket'=>$ticket,'index'=>$_REQUEST['index'])
		);
		$this->template->blocks['issue-panel'][] = new Block(
			'tickets/issueInfo.inc',
			array('ticket'=>$ticket,'issue'=>$issue,'index'=>$_REQUEST['index'],'disableButtons'=>1)
		);
	}
}