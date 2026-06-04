<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application;

abstract class PdoRepository
{
    public const TABLENAME = '';
    public const CLASSNAME = '';

    protected \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function find(array $fields=[], ?string $order=null, ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $select = 'select * from '.static::TABLENAME;
        $joins  = [];
        $where  = [];
        $params = [];

        if ($fields) {
            foreach ($fields as $k=>$v) {
                if (isset($this->columns)) {
                    if (in_array($k, $this->columns)) {
                        $where[]    = "$k=:$k";
                        $params[$k] = $v;
                    }
                }
                else {
                    $where[]    = "$k=:$k";
                    $params[$k] = $v;
                }
            }
        }
        $sql = self::buildSql($select, $joins, $where, null, $order);
        return $this->performSelect($sql, $params, $itemsPerPage, $currentPage);
    }

    public static function buildSql(string $select, array $joins, array $where, ?string $group=null, ?string $order=null): string
    {
        $sql = $select;
        if ($joins) { $sql.=' '.implode(' ', $joins); }
        if ($where) { $sql.=' where '.implode(' and ', $where); }
        if ($group) { $sql.=" group by $group"; }
        if ($order) { $sql.=" order by $order"; }
        return $sql;
    }

    protected function performSelect(string $select, array $params, ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $total = null;
        if ($itemsPerPage) {
            $currentPage = $currentPage ? $currentPage : 1;

            $sql    = "select count(*) as count from ($select) o";
            $r      = Database::query($sql, $params);
            $total  = $r[0]['count'];

            $offset = $itemsPerPage * ($currentPage-1);
            $select.= " limit $itemsPerPage offset $offset";
        }

        $rows  = [];
        $query = $this->pdo->prepare($select);
        $query->execute($params);
        $res   = $query->fetchAll(\PDO::FETCH_ASSOC);
        $model = static::CLASSNAME;
        foreach ($res as $r) { $rows[] = new $model($r); }

        return [
            'rows'  => $rows,
            'total' => $total ?? count($rows)
        ];
    }
}
