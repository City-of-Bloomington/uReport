<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Controllers;

use Application\Models\ResponseTemplate;

use Blossom\Classes\Block;
use Blossom\Classes\Controller;
use Blossom\Classes\Template;

class ResponseTemplatesController extends Controller
{
    public function index() { }

    public function update()
    {
        if (!empty($_REQUEST['id'])) {
            try { $responseTemplate = new ResponseTemplate($_REQUEST['id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        elseif (!empty($_REQUEST['category_id'])) {
            try {
                $responseTemplate = new ResponseTemplate();
                $responseTemplate->setCategory_id($_REQUEST['category_id']);
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (isset($responseTemplate)) {
            if (isset($_POST['action_id'])) {
                try {
                    $responseTemplate->handleUpdate($_POST);
                    $responseTemplate->save();

                    header('Location: '.BASE_URL.'/categories/view?category_id='.$responseTemplate->getCategory_id());
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }

            $this->template->title = $responseTemplate->getId()
                ? $this->template->_('responseTemplate_edit')
                : $this->template->_('responseTemplate_add');
            $this->template->blocks[] = new Block('responseTemplates/updateForm.inc', ['responseTemplate'=>$responseTemplate]);
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
            return;
        }
    }

    public function delete()
    {
        if (!empty($_REQUEST['id'])) {
            try {
                $responseTemplate = new ResponseTemplate($_REQUEST['id']);
                $category_id = $responseTemplate->getCategory_id();

                $responseTemplate->delete();
                header('Location: '.BASE_URL."/categories/view?category_id=$category_id");
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        header('Location: '.BASE_URL.'/categories');
        exit();
    }
}
