<?php
/**
 * @copyright 2013-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
$_SERVER['SITE_HOME'] = __DIR__;
require_once '../../bootstrap.inc';
class CustomTruncate extends PHPUnit_Extensions_Database_Operation_Truncate
{
	public function execute(PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection, PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet)
	{
		$connection->getConnection()->query("SET foreign_key_checks = 0");
		parent::execute($connection, $dataSet);
		$connection->getConnection()->query("SET foreign_key_checks = 1");
	}
}

class CustomInsert extends PHPUnit_Extensions_Database_Operation_Insert
{
	public function execute(PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection, PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet)
	{
		$connection->getConnection()->query("SET foreign_key_checks = 0");
		parent::execute($connection, $dataSet);
		$connection->getConnection()->query("SET foreign_key_checks = 1");
	}
}

abstract class DatabaseTestCase extends PHPUnit_Extensions_Database_TestCase
{
	private static $pdo = null;
	private $conn = null;

	public function getConnection()
	{
		if (!$this->conn) {
			if (!self::$pdo) {
				self::$pdo = new PDO(
					'mysql:host='.DB_HOST.';dbname='.DB_NAME,
					DB_USER,
					DB_PASS,
					array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
				);
			}
			$this->conn = $this->createDefaultDBConnection(self::$pdo, DB_NAME);
		}
		return $this->conn;
	}

	public function getSetUpOperation()
	{
		$cascadeTruncates = TRUE; //if you want cascading truncates, false otherwise
								  //if unsure choose false

		return new PHPUnit_Extensions_Database_Operation_Composite(array(
			new CustomTruncate($cascadeTruncates),
			new CustomInsert()
		));
	}
}
