<?php
/**
 * @copyright 2011-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Department $this->department
 * @param string     $this->action      The URL for posting the form
 * @param string     $this->return_url
 */
use Application\Models\ActionTable;
use Application\Models\CategoryTable;

$return_url = $this->return_url ? $this->return_url : BASE_URI.'/departments';
?>
<section class="updateDepartmentForm">
    <header>
        <h1><?= $this->template->title; ?></h1>
    </header>
    <form method="post" action="<?= $this->action; ?>">
        <fieldset>
            <legend><?= $this->_('department').' '.$this->_('info'); ?></legend>
            <input name="department_id" type="hidden" value="<?= $this->department->getId(); ?>" />
            <?php
                $helper = $this->template->getHelper('field');

                echo $helper->field([
                    'name'     => 'name',
                    'id'       => 'name',
                    'label'    => $this->_('name'),
                    'required' => true,
                    'value'    => self::escape($this->department->getName())
                ]);

                $people = $this->department->getPeople();
                if (!$people) { $people = [$_SESSION['USER']]; }

                $options = [];
                foreach ($people as $p) { $options[] = ['value'=>$p->getId(), 'label'=>self::escape($p->getFullname())]; }
                echo $helper->field([
                    'name'     => 'defaultPerson_id',
                    'id'       => 'defaultPerson_id',
                    'label'    => $this->_('defaultPerson'),
                    'value'    => $this->department->getDefaultPerson_id(),
                    'required' => true,
                    'type'     => 'select',
                    'options'  => $options
                ]);

                $options = [];
                $table   = new ActionTable();
                $list    = $table->find(['type'=>'department']);
                foreach ($list as $a) { $options[] = ['value'=>$a->getId(), 'label'=>self::escape($a->getName())]; }
                echo $helper->field([
                    'name'    => 'actions',
                    'id'      => 'actions',
                    'label'   => $this->_(['action', 'actions', 2]),
                    'value'   => array_keys($this->department->getActions()),
                    'type'    => 'checkbox',
                    'options' => $options

                ]);

                $options = [];
                $table   = new CategoryTable();
                $list    = $table->find();
                foreach ($list as $c) { $options[] = ['value'=>$c->getId(), 'label'=>self::escape($c->getName())]; }
                echo $helper->field([
                    'name'    => 'categories',
                    'id'      => 'categories',
                    'label'   => $this->_(['category', 'categories', 2]),
                    'value'   => array_keys($this->department->getCategories()),
                    'type'    => 'checkbox',
                    'options' => $options
                ]);
            ?>
        </fieldset>
        <fieldset>
            <?php
                $helper = $this->template->getHelper('saveAndCancelButtons');
                echo $helper->saveAndCancelButtons($return_url);
            ?>
        </fieldset>
    </form>
</section>
