<?php
/**
 * Singleton for the Database connection
 *
 * @copyright 2006-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Database
{
	private static $connection;

	/**
	 * @param boolean $reconnect If true, drops the connection and reconnects
	 * @return resource
	 */
	public static function getConnection($reconnect=false)
	{
		if ($reconnect) {
			self::$connection=null;
		}
		if (!self::$connection) {
			try {
				$parameters = array('host'    =>DB_HOST,
									'username'=>DB_USER,
									'password'=>DB_PASS,
									'dbname'  =>DB_NAME,
									'charset' =>'utf8',
									'options' =>array(Zend_Db::AUTO_QUOTE_IDENTIFIERS=>false));
				self::$connection = Zend_Db::factory(DB_ADAPTER,$parameters);
				self::$connection->getConnection();
			}
			catch (Exception $e) {
				die($e->getMessage());
			}
		}
		return self::$connection;
	}

	/**
	 * Returns the type of database that's being used (mysql, oracle, etc.)
	 *
	 * @return string
	 */
	public static function getType()
	{
		switch (strtolower(DB_ADAPTER)) {
			case 'pdo_mysql':
			case 'mysqli':
				return 'mysql';
				break;

			case 'pdo_oci':
			case 'oci8':
				return 'oracle';
				break;
		}

	}
}
