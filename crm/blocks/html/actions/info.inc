<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Action $this->action
 */
use Application\Models\Person;

$fields = ['id', 'name', 'description', 'template', 'replyEmail'];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f = self::escape($this->action->$get());
}
?>
<section>
    <header>
        <h1><?= $name; ?></h1>
        <div class="tools">
        <?php
            if (Person::isAllowed('actions', 'edit')) {
                $helper = $this->template->getHelper('buttonLink');
                echo $helper->buttonLink(
                    BASE_URI.'/actions/update?action_id='.$id,
                    $this->_('edit'),
                    'edit'
                );
            }
        ?>
        </div>
    </header>
    <?php
        foreach (['description', 'replyEmail', 'template'] as $f) {
            echo "
            <dl><dt>{$this->_($f)}</dt>
                <dd>{$$f}</dd>
            </dl>
            ";
        }
    ?>
</section>
