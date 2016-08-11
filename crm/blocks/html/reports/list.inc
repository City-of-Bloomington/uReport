<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\BookmarkTable;
use Blossom\Classes\Block;
?>
<div>
    <section>
        <header>
            <h1>Reports</h1>
        </header>
        <ul><li><a href="<?= BASE_URI; ?>/reports/assignments">Assignments</a></li>
            <li><a href="<?= BASE_URI; ?>/reports/categories">Categories</a></li>
            <li><a href="<?= BASE_URI; ?>/reports/sla">SLA</a></li>
            <li><a href="<?= BASE_URI; ?>/reports">Activity</a></li>
            <li><a href="<?= BASE_URI; ?>/reports/volume">Volume</a></li>
        </ul>
	</section>
	<section>
        <header>
            <h1>Activity</h1>
        </header>
        <ul><li><a href="<?= BASE_URI; ?>/reports/currentOpenTickets">Currently Open</a></li>
            <li><a href="<?= BASE_URI; ?>/reports/openedTickets">Opened Today</a></li>
            <li><a href="<?= BASE_URI; ?>/reports/closedTickets">Closed Today</a></li>
            <li><a href="<?= BASE_URI; ?>/reports/">Past Month</a></li>
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
