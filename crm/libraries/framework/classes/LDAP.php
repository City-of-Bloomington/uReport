<?php
/**
 * A class for working with entries in LDAP.
 *
 * This class is written to work with objectClass inetOrgPerson
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class LDAP
{
	private $config = array();
	private static $connection;

	private $entry = array();
	private $newEntry = true;

	// Used to keep track of changes we make to this entry.  This is because LDAP
	// requires us to send seperate modify, add, and delete commands.
	private $modifiedAttributes = array();
	private $addedAttributes = array();
	private $deletedAttributes = array();

	/**
	 * Loads an entry from the LDAP server for the given user
	 *
	 * @param array $config
	 * @param string $uid
	 */
	public function __construct($config,$uid=null)
	{
		$this->config = $config;
		$this->openConnection();
		if ($uid) {
			$result = ldap_search(self::$connection,$this->config['LDAP_DN'],"uid=$uid");
			if (ldap_count_entries(self::$connection,$result)) {
				$entries = ldap_get_entries(self::$connection, $result);
				$this->entry = $entries[0];
			}
			else {
				throw new Exception("ldap/unknownUser");
			}
			$this->newEntry = false;
		}
		else {
			$this->set('objectclass','inetOrgPerson');
		}
	}

	/**
	 * Creates the connection to the LDAP server
	 */
	private function openConnection()
	{
		if (!self::$connection) {
			if (self::$connection = ldap_connect($this->config['LDAP_SERVER'])) {
				ldap_set_option(self::$connection,LDAP_OPT_PROTOCOL_VERSION,3);
				if ($this->config['LDAP_ADMIN_USER']) {
					if (!ldap_bind(
							self::$connection,
							$this->config['LDAP_ADMIN_USER'],
							$this->config['LDAP_ADMIN_PASS']
						)) {
						throw new Exception(ldap_error(self::$connection));
					}
				}
				else {
					if (!ldap_bind(self::$connection)) {
						throw new Exception(ldap_error(self::$connection));
					}
				}
			}
			else {
				throw new Exception(ldap_error(self::$connection));
			}
		}
	}

	/**
	 * Saves any changed information back to the LDAP server
	 */
	public function save()
	{
		if (!$this->get('uid')) {
			throw new Exception('missingUID');
		}
		$dn = "uid={$this->get('uid')},{$this->config['LDAP_DN']}";
		if ($this->newEntry) {
			ldap_add(self::$connection,$dn,$this->entry);
		}
		else {
			if (count($this->modifiedAttributes)) {
				ldap_mod_replace(self::$connection,$dn,$this->modifiedAttributes)
					or die(print_r($this->modifiedAttributes).ldap_error(self::$connection));
			}
			if (count($this->addedAttributes)) {
				ldap_mod_add(self::$connection,$dn,$this->addedAttributes)
					or die(print_r($this->addedAttributes).ldap_error(self::$connection));
			}
			if (count($this->deletedAttributes)) {
				ldap_mod_del(self::$connection,$dn,$this->deletedAttributes)
					or die(print_r($this->deletedAttributes).ldap_error(self::$connection));
			}
		}
	}

	/**
	 * Escapes any problematic characters
	 * @param string $str
	 */
	private function sanitize($str)
	{
		$tmp = trim($str);
		$tmp = str_replace('\\', '\\\\', $tmp);
		$tmp = str_replace('(', '\(', $tmp);
		$tmp = str_replace(')', '\)', $tmp);
		$tmp = str_replace('*', '\*', $tmp);
		return $tmp;
	}

	/**
	 * @return string
	 */
	public function get($attributeName)
	{
		if (isset($this->entry[$attributeName])) {
			if (is_array($this->entry[$attributeName])) {
				return $this->entry[$attributeName][0];
			}
			else {
				return $this->entry[$attributeName];
			}
		}
	}

	/**
	 * Keeps track of what properties have been changed
	 *
	 * All setters should call this function.  Otherwise, we won't
	 * know what's been changed in order to do the appropriate calls in LDAP
	 * @param string $property
	 * @param string $value
	 */
	public function set($property,$value)
	{
		// LDAP always lowercases the attribute names
		$property = strtolower($property);

		if ($value) {
			switch ($property) {
				case 'userpassword':
					// Encrypt the password using SSHA
					$salt = substr(md5(time()),0,4);
					$value = '{SSHA}'.base64_encode(pack('H*',sha1($value.$salt)).$salt);
					break;
				case 'objectclass':
					// Don't perform any cleaning on objectClasses
					break;
				default:
					$value = $this->sanitize($value);
			}

			if (!isset($this->entry[$property]) || !$this->entry[$property]) {
				$this->addedAttributes[$property] = $value;
				$this->entry[$property] = $value;
			}
			elseif ($value != $this->entry[$property]) {
				$this->modifiedAttributes[$property] = $value;
				$this->entry[$property] = $value;
			}
		}
		else {
			if ($this->entry[$property]) {
				$this->entry[$property] = '';
				$this->deletedAttributes[$property] = array();
			}
		}
	}

	/**
	 * @param array $config
	 * @param string $username
	 * @param string $password
	 * @throws Exception
	 */
	public static function authenticate($config,$username,$password)
	{
		$connection = ldap_connect($config['LDAP_SERVER']) or die("Couldn't connect to LDAP");
		ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
		if (ldap_bind($connection,"uid=$username,$config[LDAP_DN]","$password")) {
			return true;
		}
	}
}
