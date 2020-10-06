<?php
/**
 * @copyright 2020 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Ticket $this->ticket
 */
declare (strict_types=1);
$ticket_id = $this->ticket->getId();
$this->template->addToAsset('scripts', BASE_URI.'/js/tickets/changeLocation-'.VERSION.'.js');
?>
<section>
    <header>
        <h1><a href="<?= BASE_URI."/tickets/view?ticket_id=$ticket_id"; ?>"><?= "#$ticket_id"; ?></a>
            <?= $this->_('change_location'); ?>
        </h1>
    </header>
    <form method="post" id="changeLocationForm">
        <input name="ticket_id" type="hidden" value="<?= $ticket_id; ?>" />
        <input name="location"  type="hidden" />
        <input name="latitude"  type="hidden" />
        <input name="longitude" type="hidden" />
    </form>
    <?php
        $this->_include('locations/findLocationForm.inc');
        $this->_include('locations/mapChooser.inc');
    ?>
</section>
