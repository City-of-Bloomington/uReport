<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Application\TableGateway;
use Zend\Db\Sql\Select;

class BookmarkTable extends TableGateway
{
	public function __construct() { parent::__construct('bookmarks', __namespace__.'\Bookmark'); }
}
