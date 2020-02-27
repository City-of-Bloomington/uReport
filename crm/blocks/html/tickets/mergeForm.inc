<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Ticket $this->parent
 * @param Ticket $this->child
 */
?>
<section class="mergeForm">
    <header>
        <h1><?= $this->_('merge_into_ticket').' #'.$this->parent->getId(); ?></h1>
    </header>
	<form method="post" action="<?= BASE_URI; ?>/tickets/merge">
		<fieldset>
            <input name="parent_ticket_id" type="hidden" value="<?= $this->parent->getId(); ?>" />
            <input name= "child_ticket_id" type="hidden" value="<?= $this->child ->getId(); ?>" />
            <input name="confirm" type="hidden" value="1" />

            <?php
                $helper = $this->template->getHelper('saveAndCancelButtons');
                echo $helper->saveAndCancelButtons(BASE_URI.'/tickets/view?ticket_id='.$this->parent->getId());
            ?>
		</fieldset>
	</form>
</section>
