<?php
/**
 * @copyright 2006-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application;

class Database
{
    private static $connections = [];

    public static function getConnection(string $db='default'): \PDO
    {
        global $DATABASES;

        if (!isset(self::$connections[$db]) && !empty($DATABASES[$db])) {
            $conf = $DATABASES[$db];
            try {
                self::$connections[$db] = new \PDO($conf['dsn'], $conf['user'], $conf['pass']);
                self::$connections[$db]->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            }
            catch (\Exception $e) { die($e->getMessage()); }
        }
        return self::$connections[$db];
    }

    public static function query(string $sql, array $params, string $db='default'): array
    {
        $pdo = self::getConnection($db);
        $q   = $pdo->prepare($sql);
        $q->execute($params);
        return $q->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function execute(string $sql, array $params, string $db='default')
    {
        $pdo = self::getConnection($db);
        $q   = $pdo->prepare($sql);
        $q->execute($params);
    }
}
