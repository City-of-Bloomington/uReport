<?php
/**
 * A class for working with entries in LDAP.
 *
 * This class is written specifically for the City of Bloomington's
 * LDAP layout.  If you are going to be doing LDAP authentication
 * with your own LDAP server, you will probably need to customize
 * the fields used in this class.
 *
 * @copyright 2006-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class LDAPEntry
{
	private static $connection;

	private $ou;

	private $uid;
	private $userPassword;

	private $givenName;
	private $sn;
	private $cn;
	private $displayName;

	private $businessCategory;
	private $departmentNumber;
	private $physicalDeliveryOfficeName;
	private $title;

	private $mail;
	private $telephoneNumber;
	private $preferredTelephoneNumber;
	private $webTelephoneNumber;
	private $facsimileTelephoneNumber;
	private $homePhone;
	private $mobile;
	private $dialupAccess;

	private $jpegPhoto;

	private $sambaLMPassword;
	private $sambaNTPassword;
	private $sambaSID;

	private $objectClasses = array();

	// Used to keep track of changes we make to this entry.  This is because LDAP
	// requires us to send seperate modify, add, and delete commands.
	private $modifiedAttributes = array();
	private $addedAttributes = array();
	private $deletedAttributes = array();


	/**
	 * Loads an entry from the LDAP server for the given user
	 * @param string $username
	 */
	public function __construct($username=null)
	{
		$this->openConnection();

		if ($username) {
			$result = ldap_search(LDAPEntry::$connection,LDAP_DN,
								  LDAP_USERNAME_ATTRIBUTE."=$username");
			if (ldap_count_entries(LDAPEntry::$connection,$result)) {
				$entries = ldap_get_entries(LDAPEntry::$connection, $result);
				$this->uid = $username;
				if (isset($entries[0]['ou'])) {
					$this->ou = $entries[0]['ou'][0];
				}
				if (isset($entries[0]['givenname'])) {
					$this->givenName = $entries[0]['givenname'][0];
				}
				if (isset($entries[0]['sn'])) {
					$this->sn = $entries[0]['sn'][0];
				}
				if (isset($entries[0]['cn'])) {
					$this->cn = $entries[0]['cn'][0];
				}
				if (isset($entries[0]['displayname'])) {
					$this->displayName = $entries[0]['displayname'][0];
				}
				if (isset($entries[0]['businesscategory'])) {
					$this->businessCategory = $entries[0]['businesscategory'][0];
				}
				if (isset($entries[0]['departmentnumber'])) {
					$this->departmentNumber = $entries[0]['departmentnumber'][0];
				}
				if (isset($entries[0]['physicaldeliveryofficename'])) {
					$this->physicalDeliveryOfficeName = $entries[0]['physicaldeliveryofficename'][0];
				}
				if (isset($entries[0]['title'])) {
					$this->title = $entries[0]['title'][0];
				}
				if (isset($entries[0]['mail'])) {
					$this->mail = $entries[0]['mail'][0];
				}
				if (isset($entries[0]['telephonenumber'])) {
					$this->telephoneNumber = $entries[0]['telephonenumber'][0];
				}
				if (isset($entries[0]['preferredtelephonenumber'])) {
					$this->preferredTelephoneNumber = $entries[0]['preferredtelephonenumber'][0];
				}
				if (isset($entries[0]['webtelephonenumber'])) {
					$this->webTelephoneNumber = $entries[0]['webtelephonenumber'][0];
				}
				if (isset($entries[0]['facsimiletelephonenumber'])) {
					$this->facsimileTelephoneNumber = $entries[0]['facsimiletelephonenumber'][0];
				}
				if (isset($entries[0]['homephone'])) {
					$this->homePhone = $entries[0]['homephone'][0];
				}
				if (isset($entries[0]['mobile'])) {
					$this->mobile = $entries[0]['mobile'][0];
				}
				if (isset($entries[0]['dialupaccess'])) {
					$this->dialupAccess = $entries[0]['dialupaccess'][0];
				}
				if (isset($entries[0]['objectclass'])) {
					$this->objectClasses = $entries[0]['objectclass'];
				}
				if (isset($entries[0]['jpegphoto'])) {
					$photo = ldap_get_values_len(LDAPEntry::$connection,
												 ldap_first_entry(LDAPEntry::$connection,$result),
												 'jpegphoto');
					$this->jpegPhoto = $photo[0];
				}
			}
			else {
				throw new Exception("ldap/unknownUser");
			}
		}
	}

	/**
	 * Creates the connection to the LDAP server
	 */
	private function openConnection()
	{
		if (!LDAPEntry::$connection) {
			if (LDAPEntry::$connection = ldap_connect(LDAP_SERVER)) {
				ldap_set_option(LDAPEntry::$connection,LDAP_OPT_PROTOCOL_VERSION,3);
				if (LDAP_ADMIN_USER) {
					if (!ldap_bind(LDAPEntry::$connection,
								   LDAP_USERNAME_ATTRIBUTE."=".LDAP_ADMIN_USER.",o=".LDAP_DOMAIN,
								   LDAP_ADMIN_PASS)) {
						throw new Exception(ldap_error(LDAPEntry::$connection));
					}
				}
				else {
					if (!ldap_bind(LDAPEntry::$connection)) {
						throw new Exception(ldap_error(LDAPEntry::$connection));
					}
				}
			}
			else {
				throw new Exception(ldap_error(LDAPEntry::$connection));
			}
		}
	}

	/**
	 * Saves any changed information back to the LDAP server
	 */
	public function save()
	{
		$dn = "uid={$this->uid},ou=people,o=".LDAP_DOMAIN;
		if (count($this->modifiedAttributes)) {
			ldap_mod_replace(LDAPEntry::$connection,$dn,$this->modifiedAttributes)
				or die(print_r($this->modifiedAttributes).ldap_error(LDAPEntry::$connection));
		}
		if (count($this->addedAttributes)) {
			ldap_mod_add(LDAPEntry::$connection,$dn,$this->addedAttributes)
				or die(print_r($this->addedAttributes).ldap_error(LDAPEntry::$connection));
		}
		if (count($this->deletedAttributes)) {
			ldap_mod_del(LDAPEntry::$connection,$dn,$this->deletedAttributes)
				or die(print_r($this->deletedAttributes).ldap_error(LDAPEntry::$connection));
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
	 * Keeps track of what properties have been changed
	 *
	 * All setters should call this function.  Otherwise, we won't
	 * know what's been changed in order to do the appropriate calls in LDAP
	 * @param string $property
	 * @param string $value
	 */
	private function changeProperty($property,$value)
	{
		if ($value) {
			if ($value != $this->{$property}) {
				if ($this->{$property}) {
					$this->modifiedAttributes[$property] = $value;
				}
				else {
					$this->addedAttributes[$property] = $value;
				}
				$this->{$property} = $value;
			}
		}
		else {
			if ($this->{$property}) {
				$this->{$property} = '';
				$this->deletedAttributes[$property] = array();
			}
		}
	}

	/**
	 * @return string
	 */
	public function getOU()
	{
		return $this->ou;
	}
	/**
	 * @return string
	 */
	public function getUID()
	{
		return $this->uid;
	}
	/**
	 * @return string
	 */
	public function getUsername()
	{
		return $this->uid;
	}
	/**
	 * @return string
	 */
	public function getFirstname()
	{
		return $this->givenName;
	}
	/**
	 * @return string
	 */
	public function getLastname()
	{
		return $this->sn;
	}
	/**
	 * @return string
	 */
	public function getCommonName()
	{
		return $this->cn;
	}
	/**
	 * @return string
	 */
	public function getDisplayName()
	{
		return $this->displayName;
	}
	/**
	 * @return string
	 */
	public function getBusinessCategory()
	{
		return $this->businessCategory;
	}
	/**
	 * @return string
	 */
	public function getDepartment()
	{
		return $this->departmentNumber;
	}
	/**
	 * @return string
	 */
	public function getOffice()
	{
		return $this->physicalDeliveryOfficeName;
	}
	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}
	/**
	 * @return string
	 */
	public function getEmail()
	{
		return $this->mail;
	}
	/**
	 * @return string
	 */
	public function getPhone()
	{
		return $this->telephoneNumber;
	}
	/**
	 * @return string
	 */
	public function getPreferredPhone()
	{
		return $this->preferredTelephoneNumber;
	}
	/**
	 * @return string
	 */
	public function getWebPhone()
	{
		return $this->webTelephoneNumber;
	}
	/**
	 * @return string
	 */
	public function getFax()
	{
		return $this->facsimileTelephoneNumber;
	}
	/**
	 * @return string
	 */
	public function getHomePhone()
	{
		return $this->homePhone;
	}
	/**
	 * @return string
	 */
	public function getCellPhone()
	{
		return $this->mobile;
	}
	/**
	 * @return string
	 */
	public function getDialup()
	{
		return $this->dialupAccess;
	}
	/**
	 * @return string
	 */
	public function getSambaLMPassword()
	{
		return $this->sambaLMPassword;
	}
	/**
	 * @return string
	 */
	public function getSambaNTPassword()
	{
		return $this->sambaNTPassword;
	}
	/**
	 * @return string
	 */
	public function getSambaSID()
	{
		return $this->sambaSID;
	}
	/**
	 * @return string
	 */
	public function getObjectClasses()
	{
		return $this->objectClasses;
	}
	/**
	 * @return raw
	 */
	public function getPhoto()
	{
		return $this->jpegPhoto;
	}

	/**
	 * @param string $string
	 */
	public function setUsername($string)
	{
		$this->changeProperty("uid",$this->sanitize($string));
	}
	/**
	 * @param string $string
	 */
	public function setFirstname($string)
	{
		$this->changeProperty("givenName",$this->sanitize($string));
	}
	/**
	 * @param string $string
	 */
	public function setLastname($string)
	{
		$this->changeProperty("sn",$this->sanitize($string));
	}
	/**
	 * @param string $string
	 */
	public function setCommonName($string)
	{
		$this->changeProperty("cn",$this->sanitize($string));
	}
	/**
	 * @param string $string
	 */
	public function setDisplayName($string)
	{
		$this->changeProperty("displayName",$this->sanitize($string));
	}
	/**
	 * @param string $string
	 */
	public function setBusinessCategory($string)
	{
		$this->changeProperty("businessCategory",$this->sanitize($string));
	}
	/**
	 * @param string $string
	 */
	public function setDepartment($string)
	{
		$this->changeProperty("departmentNumber",$this->sanitize($string));
	}
	/**
	 * @param string $string
	 */
	public function setOffice($string)
	{
		$this->changeProperty("physicalDeliveryOfficeName",$this->sanitize($string));
	}
	/**
	 * @param string $string
	 */
	public function setTitle($string)
	{
		$this->changeProperty("title",$this->sanitize($string));
	}
	/**
	 * @param string $string
	 */
	public function setEmail($string)
	{
		$this->changeProperty("mail",$this->sanitize($string));
	}
	/**
	 * @param string $string
	 */
	public function setPhone($string)
	{
		$this->changeProperty("telephoneNumber",preg_replace('/[^0-9ext\-\s]/','',$string));
	}
	/**
	 * @param string $string
	 */
	public function setPreferredPhone($string)
	{
		$this->changeProperty('preferredTelephoneNumber',
							  preg_replace('/[^0-9ext\-\s]/','',$string));
	}
	/**
	 * @param string $string
	 */
	public function setWebPhone($string)
	{
		$this->changeProperty('webTelephoneNumber',
							  preg_replace('/[^0-9ext\-\s]/','',$string));
	}
	/**
	 * @param string $string
	 */
	public function setFax($string)
	{
		$this->changeProperty('facsimileTelephoneNumber',
							  preg_replace('/[^0-9ext\-\s]/','',$string));
	}
	/**
	 * @param string $string
	 */
	public function setHomePhone($string)
	{
		$this->changeProperty('homePhone',preg_replace('/[^0-9ext\-\s]/','',$string));
	}
	/**
	 * @param string $string
	 */
	public function setCellPhone($string)
	{
		$this->changeProperty('mobile',preg_replace('/[^0-9ext\-\s]/','',$string));
	}
	/**
	 * @param string $string
	 */
	public function setDialup($string)
	{
		$this->changeProperty('dialupAccess',preg_replace('/[^0-9ext\-\s]/','',$string));
	}
	/**
	 * @param string $filePath
	 */
	public function setPhoto($filePath)
	{
		$this->changeProperty("jpegPhoto",file_get_contents($filePath));
	}
}
