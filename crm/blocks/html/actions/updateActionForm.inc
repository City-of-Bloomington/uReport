<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Action $this->action
 */
$title = $this->action->getId()
	? $this->_('edit').' '.$this->_('action')
	: $this->_('add' ).' '.$this->_('action');

$name        = self::escape($this->action->getName());
$description = self::escape($this->action->getDescription());
?>
<section class="updateActionForm">
    <header>
        <h1><?= $title; ?></h1>
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
			<input name="action_id" type="hidden" value="<?= $this->action->getId(); ?>" />
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

				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/actions');
			?>
		</fieldset>
	</form>
</section>
