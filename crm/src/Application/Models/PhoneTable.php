<?php
/**
 * @copyright 2012-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Application\TableGateway;
use Laminas\Db\Sql\Select;

class PhoneTable extends TableGateway
{
    public function __construct() { parent::__construct('peoplePhones', __namespace__.'\Phone'); }
}
