<?php
/**
 * @copyright 2014-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Application\PdoRepository;

class BookmarkTable extends PdoRepository
{
    public const TABLENAME = 'bookmarks';
    public const CLASSNAME = __namespace__.'\Bookmark';
}
