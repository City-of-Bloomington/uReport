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
?>
<section>
    <header>
        <h1><?= $categoryName; ?></h1>
    </header>
    <form method="post" action="<?= BASE_URI; ?>/responseTemplates/update">
        <fieldset><legend><?= $this->template->title; ?></legend>
            <input name="id" type="hidden" value="<?= $id; ?>" />
            <input name="category_id" type="hidden" value="<?= $category_id; ?>" />

            <dl><dt><label   for="action_id"><?= $this->_('action'); ?></label></dt>
                <dd><select name="action_id" id="action_id">
                    <?php

                        $existing = array_keys($category->getResponseTemplates());

                        $options = [];
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
                </dd>
            </dl>
            <?php
                $helper = $this->template->getHelper('field');

                echo $helper->field([
                    'name'  => 'replyEmail',
                    'id'    => 'replyEmail',
                    'label' => $this->_('replyEmail'),
                    'value' => $replyEmail
                ]);
            ?>
        </fieldset>

        <fieldset><legend><?= $this->_('responseTemplate'); ?></legend>
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
