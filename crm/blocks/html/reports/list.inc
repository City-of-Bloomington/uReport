<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
use Application\Models\BookmarkTable;
use Blossom\Classes\Block;
?>
<div>
	<section>
        <header>
            <h1><a href="<?= BASE_URI; ?>/reports"><?= $this->_('activity'); ?></a></h1>
        </header>
        <ul><li><a href="<?= BASE_URI; ?>/reports/currentOpenTickets"><?= $this->_('open_current'); ?></a></li>
            <li><a href="<?= BASE_URI; ?>/reports/openedTickets"><?= $this->_('open_today'); ?></a></li>
            <li><a href="<?= BASE_URI; ?>/reports/closedTickets"><?= $this->_('closed_today'); ?></a></li>
            <li><a href="<?= BASE_URI; ?>/reports/"><?= $this->_('past_month'); ?></a></li>
        </ul>
	</section>
    <section>
        <header>
            <h1><?= $this->_(['report', 'reports', 4]); ?></h1>
        </header>
        <ul><li><a href="<?= BASE_URI; ?>/reports/assignments"><?= $this->_(['assignment', 'assignments', 4]); ?></a></li>
            <li><a href="<?= BASE_URI; ?>/reports/categories"><?=  $this->_(['category',   'categories',  4]); ?></a></li>
            <li><a href="<?= BASE_URI; ?>/reports/staff"><?= $this->_('staff'); ?></a></li>
            <li><a href="<?= BASE_URI; ?>/reports/sla"><?= $this->_('sla'); ?></a></li>
            <li><a href="<?= BASE_URI; ?>/reports/volume"><?= $this->_('volume'); ?></a></li>
        </ul>
	</section>
	<?php
		$table = new BookmarkTable();

		$bookmarks = new Block('bookmarks/list.inc', [
            'title'     => 'Saved Reports',
            'bookmarks' => $table->find([
                'person_id' => $_SESSION['USER']->getId(),
                'type'      => 'report'
            ])
        ]);
		echo $bookmarks->render('html', $this->template);
	?>
</div>
