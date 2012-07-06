<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class ClientsController extends Controller
{
	public function __construct(Template $template)
	{
		parent::__construct($template);
		$this->template->setFilename('backend');
	}

	public function index()
	{
		$clients = new ClientList();
		$clients->find();

		$this->template->blocks[] = new Block('clients/clientList.inc',array('clientList'=>$clients));
	}

	public function update()
	{
		// Load the $client for editing
		if (isset($_REQUEST['client_id']) && $_REQUEST['client_id']) {
			try {
				$client = new Client($_REQUEST['client_id']);
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				header('Location: '.BASE_URL.'/clients');
				exit();
			}
		}
		else {
			$client = new Client();
		}

		if (isset($_POST['name'])) {
			$client->handleUpdate($_POST);
			try {
				$client->save();
				header('Location: '.BASE_URL.'/clients');
				exit();
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->blocks[] = new Block('clients/updateClientForm.inc',array('client'=>$client));
	}

	public function delete()
	{
		$client = new Client($_REQUEST['client_id']);
		$client->delete();

		header('Location: '.BASE_URL.'/clients');
		exit();
	}
}