<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Ticket $this->ticket
 */
declare (strict_types=1);
$helper  = $this->template->getHelper('field');
?>
<section>
    <header><h1>Change Visibility</h1></header>

    <form method="post">
        <fieldset>
            <input name="ticket_id" type="hidden" value="<?= $this->ticket->getId(); ?>" />

            <?php
                $c = $this->ticket->getCategory_id()
                   ? $this->ticket->getCategory()->getDisplayPermissionLevel()
                   : '';

                if ($c) { $help = $this->_('category').": $c"; }
                echo $helper->field([
                    'name'    => 'displayPermissionLevel',
                    'id'      => 'displayPermissionLevel',
                    'label'   => $this->_('displayPermissionLevel'),
                    'type'    => 'select',
                    'value'   => $this->ticket->getDisplayPermissionLevel(),
                    'options' => [['value'=>''], ['value'=>'public'], ['value'=>'private']],
                    'help'    => $help
                ]);

                $this->_include('tickets/partials/submitAndCancelButtons.inc');
            ?>
        </fieldset>
    </form>
</section>
