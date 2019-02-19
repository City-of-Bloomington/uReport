<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Application\TableGateway;
use Zend\Db\Sql\Select;

class ResponseTemplateTable extends TableGateway
{
	public function __construct() { parent::__construct('category_action_responses', __namespace__.'\ResponseTemplate'); }
}
