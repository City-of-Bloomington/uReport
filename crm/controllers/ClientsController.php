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

	/**
	 * Handles client editing
	 *
	 * Choosing a person involves going through a whole person finding process
	 * at a different url.  Once the user has chosen a new person, they will
	 * return here, passing in the person_id they have chosen
	 *
	 * @param REQUEST client_id   Existing issues are edited by passing in an Issue
	 * @param REQUEST person_id  The new contactPerson
	 */
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

		if (isset($_REQUEST['person_id'])) {
			$client->setContactPerson_id($_REQUEST['person_id']);
		}

		// Handle stuff the user POSTS
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