<?php
/**
 * @copyright 2012-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\BookmarkTable;
use Blossom\Classes\Block;
?>
<div>
	<h2>Reports</h2>
	<ul><li><a href="<?php echo BASE_URI; ?>/reports/assignments">Assignments</a></li>
		<li><a href="<?php echo BASE_URI; ?>/reports/categories">Categories</a></li>
		<li><a href="<?php echo BASE_URI; ?>/reports/sla">SLA</a></li>
		<li><a href="<?php echo BASE_URI; ?>/reports">Activity</a></li>
	</ul>
	<?php
		$table = new BookmarkTable();

		$bookmarks = new Block(
			'bookmarks/list.inc',
			array(
				'bookmarks' => $table->find([
					'person_id'=>$_SESSION['USER']->getId(),
					'type'=>'report'
				]),
				'title'=>'Saved Reports'
			)
		);
		echo $bookmarks->render('html', $this->template);
	?>
</div>
