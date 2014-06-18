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
	 * @return Issue
	 */
	private function loadIssue($id)
	{
		try {
			$issue = new Issue($id);
			return $issue;
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
	 * @param GET media_id
	 */
	public function delete()
	{
		$media = new Media($_GET['media_id']);
		$ticket = $media->getIssue()->getTicket();
		$media->delete();

		header('Location: '.$ticket->getURL());
		exit();
	}

	/**
	 * @param POST issue_id
	 * @param FILES attachment
	 */
	public function upload()
	{
		$issue = $this->loadIssue($_REQUEST['issue_id']);
		$ticket = $issue->getTicket();

		if (isset($_FILES['attachment'])) {
			try {
				$media = new Media();
				$media->setIssue($issue);
				$media->setFile($_FILES['attachment']);
				$media->save();
			}
			catch (Exception $e) {
				// Clean out any file that might have been saved
				$media->delete();

				$_SESSION['errorMessages'][] = $e;
			}

			header('Location: '.$ticket->getURL());
			exit();
		}

		$this->template->setFilename('tickets');
		$this->template->blocks['ticket-panel'][] = new Block(
			'tickets/ticketInfo.inc',
			array('ticket'=>$ticket, 'disableButtons'=>1)
		);
		$this->template->blocks['history-panel'][] = new Block(
			'tickets/history.inc',
			array('history'=>$ticket->getHistory())
		);
		$this->template->blocks['issue-panel'][] = new Block(
			'media/uploadForm.inc', array('issue'=>$issue)
		);
		$this->template->blocks['issue-panel'][] = new Block(
			'tickets/issueInfo.inc',
			array('issue'=>$issue, 'disableButtons'=>1)
		);
	}

	/**
	 * Create and cache a resized image file
	 *
	 * @param REQUEST media_id
	 * @param REQUEST size
	 */
	public function resize()
	{
		$this->template->setFilename('media');
		try {
			$media = new Media($_REQUEST['media_id']);
			$size = !empty($_REQUEST['size']) ? (int)$_REQUEST['size'] : null;
			$this->template->blocks[] = new Block(
				'media/image.inc',
				array('media'=>$media, 'size'=>$size)
			);
		}
		catch (Exception $e) {
			header('HTTP/1.1 404 Not Found', true, 404);
		}
	}
}
