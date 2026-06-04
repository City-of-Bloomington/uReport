<?php
/**
 * @copyright 2012-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Application\PdoRepository;

class ContactMethodTable extends PdoRepository
{
    public const TABLENAME = 'contactMethods';
    public const CLASSNAME = __namespace__.'\ContactMethod';

    public function find(array $fields=[], ?string $order='name', ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        return parent::find($fields, $order, $itemsPerPage, $currentPage);
    }
}
