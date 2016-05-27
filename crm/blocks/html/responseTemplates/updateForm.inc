<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param ResponseTemplate $this->responseTemplate
 */
use Application\Models\ActionTable;

$fields = ['id', 'category_id', 'action_id', 'template', 'replyEmail'];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f  = $this->responseTemplate->$get();
}
$category     = $this->responseTemplate->getCategory();
$categoryName = self::escape($category->getName());

$title = $id ? $this->_('labels.responseTemplate_edit') : $this->_('labels.responseTemplate_add');
?>
<section>
    <header>
        <h1><?= $categoryName; ?></h1>
    </header>
    <form method="post" action="<?= BASE_URI; ?>/responseTemplates/update">
        <fieldset><legend><?= $title; ?></legend>
            <input name="id" type="hidden" value="<?= $id; ?>" />
            <input name="category_id" type="hidden" value="<?= $category_id; ?>" />

            <table>
                <tr><td><label   for="action_id"><?= $this->_('labels.action'); ?></label></td>
                    <td><select name="action_id" id="action_id">
                        <?php
                            $existing = array_keys($category->getResponseTemplates());

                            $table = new ActionTable();
                            $list = $table->find();
                            foreach ($list as $a) {
                                if (!in_array($a->getId(), $existing)) {
                                    $name = self::escape($a->getName());
                                    $selected = $a->getId() == $action_id
                                        ? 'selected="true"'
                                        : '';
                                    echo "<option value=\"{$a->getId()}\" $selected>$name</option>";
                                }
                            }
                        ?>
                        </select>
                    </td>
                </tr>
                <?php
                    $autoRespondIsActive = $this->responseTemplate->autoRespondIsActive();
                ?>
                <tr><td><label><?= $this->_('labels.autoRespond'); ?></label></td>
                    <td><label>
                            <input name="autoRespond" type="radio" value="1" <?= $autoRespondIsActive ? 'checked="true"' : ''; ?> />
                            <?= $this->_('labels.active_yes'); ?>
                        </label>
                        <label>
                            <input name="autoRespond" type="radio" value="0" <?= $autoRespondIsActive ? '' : 'checked="true"'; ?> />
                            <?= $this->_('labels.active_no'); ?>
                        </label>
                    </td>
                </tr>
                <tr><td><label  for="replyEmail"><?= $this->_('labels.replyEmail'); ?></label></td>
                    <td><input name="replyEmail" id="replyEmail" value="<?= $replyEmail; ?>" />
            </table>
        </fieldset>

        <fieldset><legend><?= $this->_('labels.responseTemplate'); ?></legend>
            <div>
                <textarea name="template"><?= $template; ?></textarea>
            </div>
            <?php
                $h = $this->template->getHelper('saveAndCancelButtons');
                echo $h->saveAndCancelButtons(BASE_URI."/categories/view?category_id=$category_id");
            ?>
        </fieldset>
    </form>
</section>
