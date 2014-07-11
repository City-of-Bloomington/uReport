<?php
/**
 * A class for working with entries in LDAP.
 *
 * This class is written specifically for the City of Bloomington's
 * LDAP layout.  If you are going to be doing LDAP authentication
 * with your own LDAP server, you will probably need to customize
 * the fields used in this class.
 *
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Blossom\Classes;

class Employee implements ExternalIdentity
{
	private static $connection;
	private $config;
	private $entry;

	/**
	 * @param array $config
	 * @param string $username
	 * @param string $password
	 * @throws Exception
	 */
	public static function authenticate($username,$password)
	{
		global $DIRECTORY_CONFIG;
		$config = $DIRECTORY_CONFIG['Employee'];

		$bindUser = sprintf(str_replace('{username}','%s',$config['DIRECTORY_USER_BINDING']),$username);

		$connection = ldap_connect($config['DIRECTORY_SERVER']) or die("Couldn't connect to ADS");
		ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
		if (ldap_bind($connection,$bindUser,$password)) {
			return true;
		}
	}


	/**
	 * Loads an entry from the LDAP server for the given user
	 *
	 * @param array $config
	 * @param string $username
	 */
	public function __construct($username)
	{
		global $DIRECTORY_CONFIG;

		$this->config = $DIRECTORY_CONFIG['Employee'];
		$this->openConnection();

		$result = ldap_search(
			self::$connection,
			$this->config['DIRECTORY_BASE_DN'],
			$this->config['DIRECTORY_USERNAME_ATTRIBUTE']."=$username"
		);
		if (ldap_count_entries(self::$connection,$result)) {
			$entries = ldap_get_entries(self::$connection, $result);
			$this->entry = $entries[0];
		}
		else {
			throw new \Exception('ldap/unknownUser');
		}
	}

	/**
	 * Creates the connection to the LDAP server
	 */
	private function openConnection()
	{
		if (!self::$connection) {
			if (self::$connection = ldap_connect($this->config['DIRECTORY_SERVER'])) {
				ldap_set_option(self::$connection, LDAP_OPT_PROTOCOL_VERSION,3);
				ldap_set_option(self::$connection, LDAP_OPT_REFERRALS, 0);
				if (!empty($this->config['DIRECTORY_ADMIN_BINDING'])) {
					if (!ldap_bind(
							self::$connection,
							$this->config['DIRECTORY_ADMIN_BINDING'],
							$this->config['DIRECTORY_ADMIN_PASS']
						)) {
						throw new \Exception(ldap_error(self::$connection));
					}
				}
				else {
					if (!ldap_bind(self::$connection)) {
						throw new \Exception(ldap_error(self::$connection));
					}
				}
			}
			else {
				throw new \Exception(ldap_error(self::$connection));
			}
		}
	}

	/**
	 * @return string
	 */
	public function getUsername()	{ return $this->get('cn'); }
	public function getFirstname()	{ return $this->get('givenname'); }
	public function getLastname()	{ return $this->get('sn'); }
	public function getEmail()		{ return $this->get('mail'); }
	public function getPhone()		{ return $this->get('telephonenumber'); }
	public function getAddress()	{ return $this->get('postaladdress'); }
	public function getCity()		{ return $this->get('l'); }
	public function getState()		{ return $this->get('st'); }
	public function getZip()		{ return $this->get('postalcode'); }

	/**
	 * Returns the first scalar value from the entry's field
	 *
	 * @param string $field
	 * @return string
	 */
	private function get($field) {
		return isset($this->entry[$field][0]) ? $this->entry[$field][0] : '';
	}
}
