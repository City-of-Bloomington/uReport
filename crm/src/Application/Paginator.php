<?php
/**
 * NOTE:
 * This paginator implements the Laminas AdapterInterface only so much as we
 * need in order to make the paginator countable.  uReport uses the Laminas
 * paginator for rendering the pagination links - but never uses the
 * paginator to iterate through results.
 *
 * In the future we will remove the reliance on the Laminas paginator
 *
 * @copyright 2018-2021 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application;

use Laminas\Paginator\Adapter\AdapterInterface;

class Paginator implements AdapterInterface
{
    public $itemsPerPage      = 20;
    public $currentPageNumber = 1;
    public $totalItemCount    = 0;
    public $pageRange         = 10;

    public function __construct(int $totalItemCount, ?int $itemsPerPage=20, ?int $currentPageNumber=1, ?int $pageRange=10)
    {
        $this->totalItemCount    = $totalItemCount;
        $this->itemsPerPage      = $itemsPerPage;
        $this->currentPageNumber = $currentPageNumber < 1 ? 1 : $currentPageNumber;
        $this->pageRange         = $pageRange;
    }

    /**
     * @return \stdClass
     */
    public function getPages(): \stdClass
    {
        $pageRange  = $this->pageRange;
        $pageNumber = $this->currentPageNumber;
        $pageCount  = (int)ceil($this->totalItemCount / $this->itemsPerPage);

        if ($pageRange > $pageCount) {
            $pageRange = $pageCount;
        }

        $delta = ceil($pageRange / 2);

        if ($pageNumber - $delta > $pageCount - $pageRange) {
            $lowerBound = $pageCount - $pageRange + 1;
            $upperBound = $pageCount;
        }
        else {
            if ($pageNumber - $delta < 0) {
                $delta = $pageNumber;
            }

            $offset     = $pageNumber - $delta;
            $lowerBound = $offset + 1;
            $upperBound = $offset + $pageRange;
        }

        $pages = [];
        for ($pageNumber = $lowerBound; $pageNumber <= $upperBound; $pageNumber++) {
            $pages[$pageNumber] = $pageNumber;
        }

        $p = new \stdClass();
        $p->first   = 1;
        $p->last    = $pageCount;
        $p->current = $this->currentPageNumber;
        if ($this->currentPageNumber > 1)          { $p->previous = $this->currentPageNumber - 1; }
        if ($this->currentPageNumber < $pageCount) { $p->next     = $this->currentPageNumber + 1; }
        $p->pagesInRange = $pages;
        return $p;
    }

    public function count(): int { return $this->totalItemCount; }

    /**
     * NOT IMPLEMENTED
     *
     * uReport will always use the ticket_id from the result documents and
     * load the ticket data from the database
     */
    public function getItems($offset, $itemCountPerPage) { return []; }

}
