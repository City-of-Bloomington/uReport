<?php
/**
 * @copyright 2013-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Application\TableGateway;
use Laminas\Db\Sql\Select;

class AddressTable extends TableGateway
{
	public function __construct() { parent::__construct('peopleAddresses', __namespace__.'\Address'); }
}
