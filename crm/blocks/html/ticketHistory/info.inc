<?php
/**
 * @copyright 2011-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param array  $this->history
 * @param Ticket $this->ticket          (optional)
 * @param bool   $this->disableComments (optional)
 */
use Application\Models\Person;
?>
<section class="history">
    <header>
        <h1><?= $this->_('history'); ?></h1>
    </header>
	<?php
        if (Person::isAllowed('people', 'view')) {
            $this->_include('ticketHistory/partials/personalInfo.inc');
        }
        else {
            $this->_include('ticketHistory/partials/genericInfo.inc');
        }
	?>
</section>
