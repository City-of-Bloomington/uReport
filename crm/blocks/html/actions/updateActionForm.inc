<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Action $this->action
 */
$fields = ['id', 'name', 'description', 'template', 'replyEmail'];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f = self::escape($this->action->$get());
}
?>
<section class="updateActionForm">
    <header>
        <h1><?= $this->template->title; ?></h1>
        <p><?= $this->_('actions_description', 'messages'); ?></p>
    </header>

	<dl><dt>{enteredByPerson}</dt>
        <dd><?= $this->_('actions_enteredByPerson', 'messages'); ?></td>
    </dl>
    <dl><dt>{actionPerson}</th>
        <dd><?= $this->_('actions_actionPerson', 'messages'); ?></dd>
    </dl>

	<form method="post" action="<?= BASE_URI; ?>/actions/update">
		<fieldset><legend><?= $this->_('action'); ?></legend>
			<input name="action_id" type="hidden" value="<?= $id; ?>" />
			<?php
                $h = $this->template->getHelper('field');

                if ($this->action->getType() === 'system') {
                    echo "
                    <dl><dt><label>{$this->_('name')}</label></dt>
                        <dd>$name</dd>
                    </dl>
                    ";
                }
                else {
                    echo $h->field([
                        'label'    => $this->_('name'),
                        'name'     => 'name',
                        'id'       => 'name',
                        'value'    => $name,
                        'required' => true
                    ]);
                }

                echo $h->field([
                    'label'    => $this->_('description'),
                    'name'     => 'description',
                    'id'       => 'description',
                    'value'    => $description,
                    'required' => true
                ]);

			?>
		</fieldset>
		<fieldset><legend><?= $this->_('responseTemplate'); ?></legend>
            <?php
                echo $h->field([
                    'name'  => 'replyEmail',
                    'id'    => 'replyEmail',
                    'label' => $this->_('replyEmail'),
                    'value' => $replyEmail
                ]);

                echo $h->field([
                    'name'  => 'template',
                    'id'    => 'template',
                    'label' => $this->_('responseTemplate'),
                    'value' => $template,
                    'type'  => 'textarea'
                ]);
            ?>
		</fieldset>
		<fieldset>
            <?php
                $helper = $this->template->getHelper('saveAndCancelButtons');
                echo $helper->saveAndCancelButtons(BASE_URI.'/actions');
            ?>
		</fieldset>
	</form>
</section>
