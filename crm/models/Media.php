<?php
/**
 * Media will be attached to Issues
 *
 * Files will be stored as /data/media/YYYY/MM/DD/$media_id.ext
 * User provided filenames will be stored in the database
 *
 * @copyright 2006-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Media extends ActiveRecord
{
	protected $tablename = 'media';

	protected $issue;
	protected $person;

	/**
	 * Whitelist of accepted file types
	 */
	public static $extensions = array(
		'jpg' =>array('mime_type'=>'image/jpeg','media_type'=>'image'),
		'gif' =>array('mime_type'=>'image/gif','media_type'=>'image'),
		'png' =>array('mime_type'=>'image/png','media_type'=>'image'),
		'tiff'=>array('mime_type'=>'image/tiff','media_type'=>'image'),
		'pdf' =>array('mime_type'=>'application/pdf','media_type'=>'attachment'),
		'rtf' =>array('mime_type'=>'application/rtf','media_type'=>'attachment'),
		'doc' =>array('mime_type'=>'application/msword','media_type'=>'attachment'),
		'xls' =>array('mime_type'=>'application/msexcel','media_type'=>'attachment'),
		'gz'  =>array('mime_type'=>'application/x-gzip','media_type'=>'attachment'),
		'zip' =>array('mime_type'=>'application/zip','media_type'=>'attachment'),
		'txt' =>array('mime_type'=>'text/plain','media_type'=>'attachment'),
		'wmv' =>array('mime_type'=>'video/x-ms-wmv','media_type'=>'video'),
		'mov' =>array('mime_type'=>'video/quicktime','media_type'=>'video'),
		'rm'  =>array('mime_type'=>'application/vnd.rn-realmedia','media_type'=>'video'),
		'ram' =>array('mime_type'=>'audio/vnd.rn-realaudio','media_type'=>'audio'),
		'mp3' =>array('mime_type'=>'audio/mpeg','media_type'=>'audio'),
		'mp4' =>array('mime_type'=>'video/mp4','media_type'=>'video'),
		'flv' =>array('mime_type'=>'video/x-flv','media_type'=>'video'),
		'wma' =>array('mime_type'=>'audio/x-ms-wma','media_type'=>'audio'),
		'kml' =>array('mime_type'=>'application/vnd.google-earth.kml+xml','media_type'=>'attachment'),
		'swf' =>array('mime_type'=>'application/x-shockwave-flash','media_type'=>'attachment'),
		'eps' =>array('mime_type'=>'application/postscript','media_type'=>'attachment')
	);

	/**
	 * Populates the object with data
	 *
	 * Passing in an associative array of data will populate this object without
	 * hitting the database.
	 *
	 * Passing in a scalar will load the data from the database.
	 * This will load all fields in the table as properties of this class.
	 * You may want to replace this with, or add your own extra, custom loading
	 *
	 * @param int|array $id
	 */
	public function __construct($id=null)
	{
		if ($id) {
			if (is_array($id)) {
				$result = $id;
			}
			else {
				$zend_db = Database::getConnection();
				$sql = 'select * from media where id=?';
				$result = $zend_db->fetchRow($sql, array($id));
			}

			if ($result) {
				$this->data = $result;
			}
			else {
				throw new Exception('media/unknownMedia');
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->setUploaded('now');
			if (isset($_SESSION['USER'])) {
				$this->setPerson($_SESSION['USER']);
			}
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		// Check for required fields here.  Throw an exception if anything is missing.
		if (!$this->data['filename'])   { throw new Exception('media/missingFilename');  }
		if (!$this->data['mime_type'])  { throw new Exception('media/missingMimeType');  }
		if (!$this->data['media_type']) { throw new Exception('media/missingMediaType'); }
	}

	public function save() { parent::save(); }

	/**
	 * Deletes the file from the hard drive
	 */
	public function delete()
	{
		unlink(APPLICATION_HOME."/data/media/{$this->getDirectory()}/{$this->getInternalFilename()}");
		parent::delete();
	}
	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()         { return parent::get('id');         }
	public function getIssue_id()   { return parent::get('issue_id');   }
	public function getFilename()   { return parent::get('filename');   }
	public function getMime_type()  { return parent::get('mime_type');  }
	public function getMedia_type() { return parent::get('media_type'); }
	public function getPerson_id()  { return parent::get('person_id');  }
	public function getUploaded($f=null, DateTimeZone $tz=null) { return parent::getDateData('uploaded', $f, $tz); }

	public function getIssue()  { return parent::getForeignKeyObject('Issue',  'issue_id');  }
	public function getPerson() { return parent::getForeignKeyObject('Person', 'person_id'); }

	public function setIssue_id ($id)    { parent::setForeignKeyField ('Issue',  'issue_id',  $id); }
	public function setPerson_id($id)    { parent::setForeignKeyField ('Person', 'person_id', $id); }
	public function setIssue (Issue  $o) { parent::setForeignKeyObject('Issue',  'issue_id',  $o);  }
	public function setPerson(Person $o) { parent::setForeignKeyObject('Person', 'person_id', $o);  }
	public function setUploaded($d)      { parent::setDateData('uploaded', $d); }


	public function getType() { return $this->getMedia_type(); }
	public function getModified($f=null, DateTimeZone $tz=null) { return $this->getUploaded($f, $tz); }

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	/**
	 * Populates this object by reading information on a file
	 *
	 * This function does the bulk of the work for setting all the required information.
	 * It tries to read as much meta-data about the file as possible
	 *
	 * @param array|string Either a $_FILES array or a path to a file
	 */
	public function setFile($file)
	{
		# Handle passing in either a $_FILES array or just a path to a file
		$tempFile = is_array($file) ? $file['tmp_name'] : $file;
		$filename = is_array($file) ? basename($file['name']) : basename($file);
		if (!$tempFile) {
			throw new Exception('media/uploadFailed');
		}

		// Clean all bad characters from the filename
		$filename = $this->createValidFilename($filename);
		$this->data['filename'] = $filename;
		$extension = $this->getExtension();

		// Find out the mime type for this file
		if (array_key_exists(strtolower($extension),Media::$extensions)) {
			$this->data['mime_type']  = Media::$extensions[$extension]['mime_type'];
			$this->data['media_type'] = Media::$extensions[$extension]['media_type'];
		}
		else {
			throw new Exception('media/unknownFileType');
		}

		// Move the file where it's supposed to go
		$directory = $this->getDirectory();
		if (!is_dir(APPLICATION_HOME."/data/media/$directory")) {
			mkdir  (APPLICATION_HOME."/data/media/$directory",0777,true);
		}
		$newFile  = APPLICATION_HOME."/data/media/$directory/{$this->getInternalFilename()}";
		rename($tempFile, $newFile);
		chmod($newFile, 0666);

		// Check and make sure the file was saved
		if (!is_file($newFile)) {
			throw new Exception('media/badServerPermissions');
		}
	}

	/**
	 * Returns the path of the file, relative to /data/media
	 *
	 * Media is stored in the data directory, outside of the web directory
	 * This variable only contains the partial path.
	 * This partial path can be concat with APPLICATION_HOME or BASE_URL
	 *
	 * @return string
	 */
	public function getDirectory()
	{
		$d = getdate($this->getUploaded('U'));
		return "$d[year]/$d[mon]/$d[mday]";
	}

	/**
	 * Returns the file name used on the server
	 *
	 * We do not use the filename the user chose when saving the files.
	 * We've got a chicken-or-egg problem here.  We want to use the id
	 * as the filename, but the id doesn't exist until the info's been saved
	 * to the database.
	 *
	 * If we don't have an id yet, try and save to the database first.
	 * If that fails, we most likely don't have enough required info yet
	 *
	 * @return string
	 */
	public function getInternalFilename()
	{
		if (!$this->getId()) { $this->save(); }
		return "{$this->getId()}.{$this->getExtension()}";
	}

	/**
	 * @return string
	 */
	public function getExtension()
	{
		preg_match("/[^.]+$/",$this->data['filename'],$matches);
		return strtolower($matches[0]);
	}

	/**
	 * Returns the URL to this media
	 *
	 * @return string
	 */
	public function getURL()
	{
		return BASE_URL."/media/{$this->getDirectory()}/{$this->getInternalFilename()}";
	}

	/**
	 * @return int
	 */
	public function getFilesize()
	{
		return filesize(APPLICATION_HOME."/data/media/{$this->getDirectory()}/{$this->getInternalFilename()}");
	}

	/**
	 * Cleans a filename of any characters that might cause problems on filesystems
	 *
	 * @return string
	 */
	public static function createValidFilename($string)
	{
		// No bad characters
		$string = preg_replace('/[^A-Za-z0-9_\.\s]/','',$string);

		// Convert spaces to underscores
		$string = preg_replace('/\s+/','_',$string);

		// Lower case any file extension
		if (preg_match('/(^.*\.)([^\.]+)$/',$string,$matches)) {
			$string = $matches[1].strtolower($matches[2]);
		}

		return $string;
	}
}
