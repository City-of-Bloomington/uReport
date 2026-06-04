<?php
/**
 * @copyright 2011-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Application\PdoRepository;

class DepartmentTable extends PdoRepository
{
    public const TABLENAME = 'departments';
    public const CLASSNAME = __namespace__.'\Department';

    public function find(array $fields=[], ?string $order='name', ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        return parent::find($fields, $order, $itemsPerPage, $currentPage);
    }
}
