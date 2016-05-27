<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Category $this->category
 */
use Application\Models\Person;

$templates = $this->category->getResponseTemplates();

$helper = $this->template->getHelper('buttonLink');
$addButton = '';
if (Person::isAllowed('responseTemplates', 'add')) {
    $addButton = $helper->buttonLink(
        BASE_URI.'/responseTemplates/update?category_id='.$this->category->getId(),
        $this->_('labels.responseTemplate_add'),
        'add'
    );
}
?>
<section>
    <header>
        <h1><?= $this->_(['labels.responseTemplate', 'labels.responseTemplates', count($templates)]); ?></h1>
        <div class="tools"><?= $addButton; ?></div>
    </header>

    <table>
        <thead>
            <tr><th><?= $this->_('labels.action'    ); ?></th>
                <th><?= $this->_('labels.replyEmail'); ?></th>
                <th><?= $this->_('labels.autoRespond'); ?></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php
            $userCanEdit   = Person::isAllowed('responseTemplates', 'edit');
            $userCanDelete = Person::isAllowed('responseTemplates', 'delete');
            foreach ($templates as $t) {
                $editButton   = '';
                $deleteButton = '';
                if ($userCanEdit) {
                    $editButton = $helper->buttonLink(
                        BASE_URI.'/responseTemplates/update?id='.$t->getId(),
                        $this->_('labels.edit'),
                        'edit'
                    );
                }
                if ($userCanDelete) {
                    $deleteButton = $helper->buttonLink(
                        BASE_URI.'/responseTemplates/delete?id='.$t->getId(),
                        $this->_('labels.delete'),
                        'delete'
                    );
                }
                $action      = self::escape($t->getAction());
                $replyEmail  = self::escape($t->getReplyEmail());
                $autoRespond = $t->autoRespondIsActive() ? $this->_('labels.active_yes') : $this->_('labels.active_no');
                echo "
                <tr><td>$action</td>
                    <td>$replyEmail</td>
                    <td>$autoRespond</td>
                    <td>$editButton $deleteButton</td>
                </tr>
                ";
            }
        ?>
        </tbody>
    </table>
</section>
