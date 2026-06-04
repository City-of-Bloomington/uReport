<?php
/**
 * @copyright 2012-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Controllers;

use Application\Models\CategoryGroup;
use Application\Models\CategoryGroupTable;

use Application\Block;
use Application\Controller;
use Application\Template;

class CategoryGroupsController extends Controller
{
    public function index()
    {
        $table = new CategoryGroupTable();
        $list  = $table->find();
        $this->template->title = $this->template->_(['categoryGroup', 'categoryGroups', $list['total']]);
        $this->template->blocks[] = new Block('categoryGroups/list.inc', ['categoryGroupList'=>$list['rows']]);
    }

    public function update()
    {
        // Load the $client for editing
        if (!empty($_REQUEST['categoryGroup_id'])) {
            try {
                $group = new CategoryGroup($_REQUEST['categoryGroup_id']);
            }
            catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e;
                header('Location: '.BASE_URL.'/categoryGroups');
                exit();
            }
        }
        else {
            $group = new CategoryGroup();
        }

        if (isset($_POST['name'])) {
            $group->handleUpdate($_POST);
            try {
                $group->save();
                header('Location: '.BASE_URL.'/categoryGroups');
                exit();
            }
            catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e;
            }
        }

        $this->template->title = $group->getId()
            ? $this->template->_('categoryGroup_edit')
            : $this->template->_('categoryGroup_add');
        $this->template->blocks[] = new Block('categoryGroups/updateForm.inc', ['categoryGroup'=>$group]);
    }

    public function reorder()
    {
        if (isset(   $_POST['categoryGroups'])) {
            foreach ($_POST['categoryGroups'] as $id=>$order) {
                try {
                    $group = new CategoryGroup($id);
                    $group->setOrdering($order);
                    $group->save();
                }
                catch (\Exception $e) {
                    $_SESSION['errorMessages'][] = $e;
                }
            }
            header('Location: '.BASE_URL.'/categoryGroups');
            exit();
        }

        $this->template->title = $this->template->_('reorder');
        $this->template->blocks[] = new Block('categoryGroups/reorderForm.inc');
    }

    public function delete()
    {
        try {
            $group = new CategoryGroup($_REQUEST['categoryGroup_id']);
            $group->delete();
        }
        catch (\Exception $e) {
            $_SESSION['errorMessages'][] = $e;
        }
        header('Location: '.BASE_URL.'/categoryGroups');
        exit();
    }
}
