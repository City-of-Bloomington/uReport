<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array  $this->history
 * @param Ticket $this->ticket          (optional)
 * @param bool   $this->disableComments (optional)
 */
use Application\Models\Person;
use Blossom\Classes\Block;
?>
<section class="history">
    <header>
        <h1><?= $this->_('history'); ?></h1>
        <?php
            if (!$this->disableButtons && $this->ticket) {
                $this->_include('tickets/partials/actionLinks.inc');
            }
        ?>
    </header>
	<?php
		foreach ($this->history as $event) {
            $description = self::escape($event->getDescription($this->template));

			echo "
			<article class=\"historyItem\">
				<h1>{$event->getActionDate(DATE_FORMAT)} $description</h1>
			";

			if (!$this->disableComments) {
                $notes = self::escape($event->getNotes());
				echo "<p>$notes</p>";
			}

			$action   = $event->getAction();
			$issue_id = $event->getIssue_id();
			if ($action->getType() === 'system' && $issue_id) {
                $block = new Block('tickets/issueInfo.inc', ['issue'=>$event->getIssue()]);
                echo $block->render('html',$this->template);
			}

			echo "</article>";
		}
	?>
</section>
