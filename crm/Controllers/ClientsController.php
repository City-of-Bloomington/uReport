<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;

use Application\Models\Client;
use Application\Models\ClientTable;

use Blossom\Classes\Block;
use Blossom\Classes\Controller;
use Blossom\Classes\Template;

class ClientsController extends Controller
{
	public function index()
	{
		$t = new ClientTable();
		$clients = $t->find();

		$this->template->blocks[] = new Block('clients/clientList.inc', ['clientList'=>$clients]);
	}

	/**
	 * Handles client editing
	 *
	 * Choosing a person involves going through a whole person finding process
	 * at a different url.  Once the user has chosen a new person, they will
	 * return here, passing in the person_id they have chosen
	 *
	 * @param REQUEST client_id
	 * @param REQUEST person_id  The new contactPerson
	 */
	public function update()
	{
		// Load the $client for editing
		if (isset($_REQUEST['client_id']) && $_REQUEST['client_id']) {
			try {
				$client = new Client($_REQUEST['client_id']);
			}
			catch (\Exception $e) {
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
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->blocks[] = new Block('clients/updateClientForm.inc', ['client'=>$client]);
	}

	public function delete()
	{
		$client = new Client($_REQUEST['client_id']);
		$client->delete();

		header('Location: '.BASE_URL.'/clients');
		exit();
	}
}
