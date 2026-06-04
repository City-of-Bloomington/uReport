<?php
/**
 * Displays page navigation for any list
 *
 * @copyright 2007-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param int totalItemCount
 * @param int itemsPerPage
 * @param int pageNumber
 * @param int pageRange
 */
use Application\Url;

$totalItemCount = $this->totalItemCount ??  0;
$itemsPerPage   = $this->itemsPerPage   ?? 20;
$pageNumber     = $this->pageNumber     ??  1;
$pageRange      = $this->pageRange      ?? 10;


$pageCount  = (int)ceil($totalItemCount / $itemsPerPage);

if ($pageRange > $pageCount) { $pageRange = $pageCount; }

$delta = ceil($pageRange / 2);

if ($pageNumber - $delta > $pageCount - $pageRange) {
    $lowerBound = $pageCount - $pageRange + 1;
    $upperBound = $pageCount;
}
else {
    if ($pageNumber - $delta < 0) { $delta = $pageNumber; }

    $offset     = $pageNumber - $delta;
    $lowerBound = $offset + 1;
    $upperBound = $offset + $pageRange;
}

if ($pageCount > 1) {
    $url = new Url(Url::current_url(BASE_HOST));
    $url->purgeEmptyParameters();

    echo '<ul class="pageNavigation">';

    $url->page = 1;
    echo "<li><a href=\"$url\" class=\"first\">First</a></li>";

    $url->page = $pageNumber - 1;
    echo "<li><a href=\"$url\" class=\"previous\">Back</a></li>";

    for ($i=$lowerBound; $i<=$upperBound; $i++) {
        $url->page = $i;
        $class     = ($pageNumber == $i) ? 'class="current"' : '';
        echo "<li><a href=\"$url\" $class>$i</a></li>";

    }

    if ($pageNumber + 1 < $pageCount) {
        $url->page = $pageNumber + 1;
        echo "<li><a href=\"$url\" class=\"next\">Next</a></li>";

        $url->page = $pageCount;
        echo "<li><a href=\"$url\" class=\"last\">Last</a></li>";
    }

    echo '</ul>';
}
