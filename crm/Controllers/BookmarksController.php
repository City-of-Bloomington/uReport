<?php
/**
 * @copyright 2013-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;

use Application\Models\Bookmark;
use Application\Models\BookmarkTable;

use Blossom\Classes\Block;
use Blossom\Classes\Controller;
use Blossom\Classes\Template;

class BookmarksController extends Controller
{
	public function __construct(Template $template)
	{
		parent::__construct($template);
		$this->template->setFilename('backend');
		$this->template->blocks['panel-one'][] = new Block('account/myAccount.inc');
	}

	public function index()
	{
		$table = new BookmarkTable();
		$list  = $table->find(['person_id' => $_SESSION['USER']->getId()]);
		$this->template->title = $this->template->_(['bookmark', 'bookmarks', count($list)]);
		$this->template->blocks[] = new Block('bookmarks/list.inc', ['bookmarks'=>$list]);
	}

	public function update()
	{
		if (isset($_REQUEST['bookmark_id'])) {
			try {
				$bookmark = new Bookmark($_REQUEST['bookmark_id']);
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				header('Location: '.BASE_URL.'/bookmarks');
				exit();
			}
		}
		else {
			$bookmark = new Bookmark();
		}

		if (isset($_POST['requestUri'])) {
			$bookmark->handleUpdate($_POST);
			try {
				$bookmark->save();

				// Bookmarks are always created in place.
				// So, the Uri for the bookmark is the same Uri to
				// go back to the previous screen.
				header('Location: '.$bookmark->getFullUrl());
				exit();
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->title = $bookmark->getId()
            ? $this->template->_('bookmark_edit')
            : $this->template->_('bookmark_add' );
		$this->template->blocks[] = new Block('bookmarks/updateForm.inc', ['bookmark'=>$bookmark]);
	}

	public function delete()
	{
		try {
			$bookmark = new Bookmark($_REQUEST['bookmark_id']);
			$bookmark->delete();
		}
		catch (\Exception $e) {
			$_SESSION['errorMessages'][] = $e;
		}

		$return_url = !empty($_REQUEST['return_url']) ? $_REQUEST['return_url'] : BASE_URL.'/bookmarks';
		header("Location: $return_url");
		exit();
	}
}
